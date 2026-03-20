package ca.ets.tch57.myapplication;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;
import android.widget.CheckBox;
import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.appcompat.app.AlertDialog;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.google.android.material.floatingactionbutton.FloatingActionButton;
import com.google.firebase.auth.FirebaseAuth;
import com.google.firebase.auth.FirebaseUser;
import com.google.firebase.firestore.FirebaseFirestore;
import com.google.firebase.firestore.QueryDocumentSnapshot;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class TasksFragment extends Fragment {

    private RecyclerView rvTasks;
    private TextView tvEmptyTasks;
    private FloatingActionButton fabAddTask;
    private TaskAdapter taskAdapter;
    private List<Task> taskList;
    private FirebaseAuth mAuth;
    private FirebaseFirestore db;

    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_tasks, container, false);

        mAuth = FirebaseAuth.getInstance();
        db = FirebaseFirestore.getInstance();

        rvTasks = view.findViewById(R.id.rvTasks);
        tvEmptyTasks = view.findViewById(R.id.tvEmptyTasks);
        fabAddTask = view.findViewById(R.id.fabAddTask);

        taskList = new ArrayList<>();
        taskAdapter = new TaskAdapter(taskList);
        rvTasks.setLayoutManager(new LinearLayoutManager(getContext()));
        rvTasks.setAdapter(taskAdapter);

        fabAddTask.setOnClickListener(v -> showAddTaskDialog());

        return view;
    }

    @Override
    public void onResume() {
        super.onResume();
        loadTasksFromFirestore();
    }

    private void loadTasksFromFirestore() {
        FirebaseUser user = mAuth.getCurrentUser();
        if (user == null) return;

        db.collection("users").document(user.getUid()).collection("tasks")
                .get()
                .addOnCompleteListener(task -> {
                    if (task.isSuccessful()) {
                        taskList.clear();
                        for (QueryDocumentSnapshot document : task.getResult()) {
                            Task t = document.toObject(Task.class);
                            t.setId(document.getId());
                            taskList.add(t);
                        }
                        taskAdapter.notifyDataSetChanged();
                        updateEmptyState();
                    }
                });
    }

    private void updateEmptyState() {
        if (tvEmptyTasks == null || rvTasks == null) return;
        if (taskList == null || taskList.isEmpty()) {
            rvTasks.setVisibility(View.GONE);
            tvEmptyTasks.setVisibility(View.VISIBLE);
        } else {
            rvTasks.setVisibility(View.VISIBLE);
            tvEmptyTasks.setVisibility(View.GONE);
        }
    }

    private void showAddTaskDialog() {
        AlertDialog.Builder builder = new AlertDialog.Builder(requireContext());
        builder.setTitle("Nouvelle tâche");

        View viewInflated = LayoutInflater.from(getContext()).inflate(R.layout.dialog_add_task, (ViewGroup) getView(), false);
        final EditText input = viewInflated.findViewById(R.id.etTaskName);
        builder.setView(viewInflated);

        builder.setPositiveButton("Ajouter", (dialog, which) -> {
            String taskName = input.getText().toString().trim();
            if (!taskName.isEmpty()) {
                saveTaskToFirestore(taskName);
            }
        });
        builder.setNegativeButton("Annuler", (dialog, which) -> dialog.cancel());

        builder.show();
    }

    private void saveTaskToFirestore(String name) {
        FirebaseUser user = mAuth.getCurrentUser();
        if (user == null) return;

        Map<String, Object> taskMap = new HashMap<>();
        taskMap.put("name", name);
        taskMap.put("completed", false);

        db.collection("users").document(user.getUid()).collection("tasks")
                .add(taskMap)
                .addOnSuccessListener(documentReference -> loadTasksFromFirestore())
                .addOnFailureListener(e -> Toast.makeText(getContext(), "Erreur lors de l'ajout", Toast.LENGTH_SHORT).show());
    }

    private class TaskAdapter extends RecyclerView.Adapter<TaskViewHolder> {
        private List<Task> tasks;

        public TaskAdapter(List<Task> tasks) {
            this.tasks = tasks;
        }

        @NonNull
        @Override
        public TaskViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
            View v = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_task_checkbox, parent, false);
            return new TaskViewHolder(v);
        }

        @Override
        public void onBindViewHolder(@NonNull TaskViewHolder holder, int position) {
            Task task = tasks.get(position);
            holder.tvTaskName.setText(task.getName());
            
            holder.cbTask.setOnCheckedChangeListener(null);
            holder.cbTask.setChecked(task.isCompleted());

            holder.cbTask.setOnCheckedChangeListener((buttonView, isChecked) -> {
                if (isChecked) {
                    FirebaseUser user = mAuth.getCurrentUser();
                    if (user != null) {
                        db.collection("users").document(user.getUid()).collection("tasks")
                            .document(task.getId())
                            .delete()
                            .addOnSuccessListener(aVoid -> {
                                int currentPos = holder.getAdapterPosition();
                                if (currentPos != RecyclerView.NO_POSITION) {
                                    taskList.remove(currentPos);
                                    notifyItemRemoved(currentPos);
                                    updateEmptyState();
                                }
                            });
                    }
                }
            });
        }

        @Override
        public int getItemCount() {
            return tasks.size();
        }
    }

    private static class TaskViewHolder extends RecyclerView.ViewHolder {
        TextView tvTaskName;
        CheckBox cbTask;
        public TaskViewHolder(@NonNull View itemView) {
            super(itemView);
            tvTaskName = itemView.findViewById(R.id.tvTaskName);
            cbTask = itemView.findViewById(R.id.cbTask);
        }
    }
}
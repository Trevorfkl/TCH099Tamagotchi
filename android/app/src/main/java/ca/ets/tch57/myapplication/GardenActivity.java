package ca.ets.tch57.myapplication;

import android.os.Bundle;
import android.view.View;
import android.widget.TextView;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.google.android.material.bottomsheet.BottomSheetDialog;
import java.util.ArrayList;
import java.util.List;

public class GardenActivity extends AppCompatActivity {

    private RecyclerView rvGardenGrid;
    private GardenAdapter adapter;
    private List<GardenCell> cellList;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_garden);

        rvGardenGrid = findViewById(R.id.rvGardenGrid);
        initData();
        setupRecyclerView();
    }

    private void initData() {
        cellList = new ArrayList<>();
        for (int i = 1; i <= 35; i++) {
            // Hardcoding some data for demo purposes
            Integer plantResId = null;
            int tasksCompleted = 0;
            int totalTasks = 3;

            if (i == 1) {
                plantResId = android.R.drawable.ic_menu_report_image;
                tasksCompleted = 0;
            } else if (i == 2) {
                plantResId = android.R.drawable.ic_menu_report_image;
                tasksCompleted = 1;
            } else if (i == 3) {
                plantResId = android.R.drawable.ic_menu_report_image;
                tasksCompleted = 3;
            }

            cellList.add(new GardenCell(i, plantResId, tasksCompleted, totalTasks));
        }
    }

    private void setupRecyclerView() {
        adapter = new GardenAdapter(cellList, cell -> {
            if (cell.getPlantResId() == null) {
                showPlantPickerBottomSheet(cell);
            } else {
                showTaskBottomSheet(cell);
            }
        });
        rvGardenGrid.setLayoutManager(new GridLayoutManager(this, 7));
        rvGardenGrid.setAdapter(adapter);
    }

    private void showPlantPickerBottomSheet(GardenCell cell) {
        BottomSheetDialog dialog = new BottomSheetDialog(this);
        View view = getLayoutInflater().inflate(R.layout.fragment_plant_picker, null);
        
        view.findViewById(R.id.btnPlanter).setOnClickListener(v -> {
            cell.setPlantResId(android.R.drawable.ic_menu_report_image);
            adapter.notifyItemChanged(cell.getDayNumber() - 1);
            dialog.dismiss();
        });

        dialog.setContentView(view);
        dialog.show();
    }

    private void showTaskBottomSheet(GardenCell cell) {
        BottomSheetDialog dialog = new BottomSheetDialog(this);
        View view = getLayoutInflater().inflate(R.layout.fragment_square_tasks, null);

        // Highlight growth stage based on progress
        TextView stage1 = view.findViewById(R.id.tvStage1);
        TextView stage2 = view.findViewById(R.id.tvStage2);
        TextView stage3 = view.findViewById(R.id.tvStage3);

        float progress = (float) cell.getTasksCompleted() / cell.getTotalTasks();
        if (progress == 0) {
            stage1.setBackgroundColor(getResources().getColor(R.color.stage_1_green));
        } else if (progress < 1.0) {
            stage2.setBackgroundColor(getResources().getColor(R.color.stage_2_green));
        } else {
            stage3.setBackgroundColor(getResources().getColor(R.color.primary_green));
        }

        dialog.setContentView(view);
        dialog.show();
    }
}
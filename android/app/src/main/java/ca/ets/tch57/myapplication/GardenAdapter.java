package ca.ets.tch57.myapplication;

import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import java.util.List;

public class GardenAdapter extends RecyclerView.Adapter<GardenAdapter.ViewHolder> {

    private final List<GardenCell> cells;
    private final OnCellClickListener listener;

    public interface OnCellClickListener {
        void onCellClick(GardenCell cell);
    }

    public GardenAdapter(List<GardenCell> cells, OnCellClickListener listener) {
        this.cells = cells;
        this.listener = listener;
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_garden_cell, parent, false);
        return new ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        GardenCell cell = cells.get(position);
        holder.tvDayNumber.setText(String.valueOf(cell.getDayNumber()));

        // Checkerboard and progress logic
        int row = position / 7;
        int col = position % 7;
        boolean isDark = (row + col) % 2 == 1;

        if (cell.getTotalTasks() == 0 || cell.getTasksCompleted() == 0) {
            // No plant: pure checkerboard
            holder.itemView.setBackgroundColor(Color.parseColor(isDark ? "#B8D9B0" : "#E8F5E2"));
        } else if (cell.getTasksCompleted() < cell.getTotalTasks()) {
            // Stage 2: checkerboard but slightly darker
            holder.itemView.setBackgroundColor(Color.parseColor(isDark ? "#8DC487" : "#B8D9B0"));
        } else {
            // Stage 3: complete, checkerboard with dark green
            holder.itemView.setBackgroundColor(Color.parseColor(isDark ? "#4A7A4F" : "#6A9E6F"));
        }

        holder.itemView.setOnClickListener(v -> listener.onCellClick(cell));
    }

    @Override
    public int getItemCount() {
        return cells.size();
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        TextView tvDayNumber;

        public ViewHolder(@NonNull View itemView) {
            super(itemView);
            tvDayNumber = itemView.findViewById(R.id.tvDayNumber);
        }
    }
}
package ca.ets.tch57.myapplication;

public class GardenCell {
    private int dayNumber;
    private Integer plantResId;
    private int tasksCompleted;
    private int totalTasks;

    public GardenCell(int dayNumber, Integer plantResId, int tasksCompleted, int totalTasks) {
        this.dayNumber = dayNumber;
        this.plantResId = plantResId;
        this.tasksCompleted = tasksCompleted;
        this.totalTasks = totalTasks;
    }

    public int getDayNumber() {
        return dayNumber;
    }

    public void setDayNumber(int dayNumber) {
        this.dayNumber = dayNumber;
    }

    public Integer getPlantResId() {
        return plantResId;
    }

    public void setPlantResId(Integer plantResId) {
        this.plantResId = plantResId;
    }

    public int getTasksCompleted() {
        return tasksCompleted;
    }

    public void setTasksCompleted(int tasksCompleted) {
        this.tasksCompleted = tasksCompleted;
    }

    public int getTotalTasks() {
        return totalTasks;
    }

    public void setTotalTasks(int totalTasks) {
        this.totalTasks = totalTasks;
    }
}
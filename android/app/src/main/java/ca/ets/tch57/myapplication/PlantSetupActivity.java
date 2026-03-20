package ca.ets.tch57.myapplication;

import android.os.Bundle;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import com.google.android.material.button.MaterialButton;
import com.google.android.material.textfield.TextInputEditText;

public class PlantSetupActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_plant_setup);

        int dayNumber = getIntent().getIntExtra("dayNumber", 0);
        
        TextView tvTitle = findViewById(R.id.tvSetupTitle);
        tvTitle.setText("Jour " + dayNumber);

        TextInputEditText etTotalTasks = findViewById(R.id.etTotalTasks);
        MaterialButton btnConfirm = findViewById(R.id.btnConfirmSetup);
        MaterialButton btnBack = findViewById(R.id.btnBackSetup);

        btnConfirm.setOnClickListener(v -> {
            String tasks = etTotalTasks.getText().toString();
            if (!tasks.isEmpty()) {
                Toast.makeText(this, "Plante configurée pour le jour " + dayNumber, Toast.LENGTH_SHORT).show();
                finish();
            } else {
                Toast.makeText(this, "Veuillez entrer un nombre de tâches", Toast.LENGTH_SHORT).show();
            }
        });

        btnBack.setOnClickListener(v -> finish());
    }
}
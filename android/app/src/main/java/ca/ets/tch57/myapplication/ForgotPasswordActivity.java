package ca.ets.tch57.myapplication;

import android.os.Bundle;
import android.text.TextUtils;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import com.google.android.material.button.MaterialButton;
import com.google.android.material.textfield.TextInputEditText;
import com.google.firebase.auth.FirebaseAuth;

public class ForgotPasswordActivity extends AppCompatActivity {

    private TextInputEditText etEmail;
    private MaterialButton btnSend, btnBack;
    private FirebaseAuth mAuth;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_forgot_password);

        mAuth = FirebaseAuth.getInstance();
        etEmail = findViewById(R.id.etForgotEmail);
        btnSend = findViewById(R.id.btnSendReset);
        btnBack = findViewById(R.id.btnForgotBack);

        btnSend.setOnClickListener(v -> {
            String email = etEmail.getText().toString().trim();

            if (TextUtils.isEmpty(email)) {
                Toast.makeText(this, "Veuillez entrer votre courriel", Toast.LENGTH_SHORT).show();
                return;
            }

            mAuth.sendPasswordResetEmail(email)
                    .addOnCompleteListener(task -> {
                        if (task.isSuccessful()) {
                            Toast.makeText(ForgotPasswordActivity.this, "Courriel de réinitialisation envoyé !", Toast.LENGTH_LONG).show();
                            finish();
                        } else {
                            String error = task.getException() != null ? task.getException().getMessage() : "Erreur inconnue";
                            Toast.makeText(ForgotPasswordActivity.this, "Erreur : " + error, Toast.LENGTH_LONG).show();
                        }
                    });
        });

        btnBack.setOnClickListener(v -> finish());
    }
}
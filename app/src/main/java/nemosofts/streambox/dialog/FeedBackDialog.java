package nemosofts.streambox.dialog;

import static android.view.WindowManager.LayoutParams.MATCH_PARENT;
import static android.view.WindowManager.LayoutParams.WRAP_CONTENT;

import android.app.Activity;
import android.app.Dialog;
import android.view.Window;
import android.widget.EditText;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.nemosofts.material.ProgressDialog;
import androidx.nemosofts.utils.NetworkUtils;

import java.util.Objects;

import nemosofts.streambox.R;
import nemosofts.streambox.callback.Method;
import nemosofts.streambox.executor.LoadStatus;
import nemosofts.streambox.interfaces.StatusListener;
import nemosofts.streambox.utils.ApplicationUtil;
import nemosofts.streambox.utils.helper.Helper;
import nemosofts.streambox.utils.helper.SPHelper;

public class FeedBackDialog {

    private final Helper helper;
    private final SPHelper spHelper;
    private Dialog dialog;
    private final Activity ctx;
    private final ProgressDialog progressDialog;

    public FeedBackDialog(@NonNull Activity ctx) {
        this.ctx = ctx;
        helper = new Helper(ctx);
        spHelper = new SPHelper(ctx);
        progressDialog = new ProgressDialog(ctx, true);
    }

    public void showDialog(String title) {
        if(spHelper.isLogged()) {
            dialog = new Dialog(ctx);
            dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            dialog.setContentView(R.layout.dialog_feed_back);
            EditText messages = dialog.findViewById(R.id.et_messages);
            dialog.findViewById(R.id.tv_cancel).setOnClickListener(view -> dismissDialog());
            dialog.findViewById(R.id.iv_close).setOnClickListener(view -> dismissDialog());
            dialog.findViewById(R.id.tv_submit).setOnClickListener(view -> {
                if(messages.getText().toString().trim().isEmpty()) {
                    messages.setError(ctx.getString(R.string.please_describe_the_problem));
                    messages.requestFocus();
                } else {
                    loadReportSubmit(messages.getText().toString(), title);
                }
            });
            Objects.requireNonNull(dialog.getWindow()).setBackgroundDrawableResource(android.R.color.transparent);
            dialog.getWindow().getAttributes().windowAnimations = R.style.dialogAnimation;
            dialog.show();
            Window window = dialog.getWindow();
            window.setLayout(MATCH_PARENT, WRAP_CONTENT);
        }
    }

    private void loadReportSubmit(String reportMessages, String reportTitle) {
        if (!NetworkUtils.isConnected(ctx)){
            Toast.makeText(ctx, ctx.getString(R.string.err_internet_not_connected), Toast.LENGTH_SHORT).show();
            return;
        }
        LoadStatus loadFav = new LoadStatus(new StatusListener() {
            @Override
            public void onStart() {
                progressDialog.show();
            }

            @Override
            public void onEnd(String success, String reportSuccess, String message) {
                dismissProgress();
                if (success.equals("1")) {
                    if (reportSuccess.equals("1")) {
                        Toast.makeText(ctx, message, Toast.LENGTH_SHORT).show();
                        dismissDialog();
                    } else {
                        Toast.makeText(ctx, message, Toast.LENGTH_SHORT).show();
                        ApplicationUtil.log("FeedBackDialog", "Report submission failed logically: " + message, null);
                    }
                } else {
                    String errorMsg = ctx.getString(R.string.err_server_not_connected);
                    if (message != null && !message.isEmpty()) {
                        errorMsg += ": " + message;
                    }
                    Toast.makeText(ctx, errorMsg, Toast.LENGTH_SHORT).show();
                    ApplicationUtil.log("FeedBackDialog", "Server error during submission: " + message, null);
                }
            }
        }, helper.getAPIRequestNSofts(Method.METHOD_REPORT, reportTitle, reportMessages,
                spHelper.getUserName(), spHelper.getPassword()));
        loadFav.execute();
    }

    private void dismissProgress() {
        if (progressDialog != null && progressDialog.isShowing()) {
            try {
                progressDialog.dismiss();
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
    }

    private void dismissDialog() {
        if (dialog != null && dialog.isShowing()){
            try {
                dialog.dismiss();
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
    }
}

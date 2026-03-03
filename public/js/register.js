// Camera setup code (should be in your JS)
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const captureBtn = document.getElementById('captureBtn');
const liveImagePreview = document.getElementById('live_image_preview');

// Access webcam
if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(function (stream) {
            video.srcObject = stream;
            video.play();
        })
        .catch(function (err) {
            console.error("Cannot access webcam: ", err);
        });
}

// Capture button event
captureBtn.addEventListener('click', function () {
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    const dataURL = canvas.toDataURL("image/jpeg");

    liveImagePreview.src = dataURL;
    liveImagePreview.classList.remove('hidden');

    window.liveImageBase64 = dataURL;
});

// ----------------------------------------------------------------------------------

document.getElementById("registerForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const messageBox = document.getElementById("formMessage");
    messageBox.classList.add("hidden");

    const username = document.getElementById("username").value;

    const cnic = document.getElementById("cnic").value.trim();

     if (!/^\d{13}$/.test(cnic)) {
    showMessage("CNIC must be exactly 13 digits (numbers only)", "error");
    return;
    }

    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    const cnicImageInput = document.getElementById("cnic_image");

    if (!window.emailVerified) {
        showMessage("Please verify your email first!", "error");
        return;
    }

    if (!cnicImageInput.files || cnicImageInput.files.length === 0) {
        showMessage("Please upload CNIC image!", "error");
        return;
    }

    if (!window.liveImageBase64) {
        showMessage("Please capture your live selfie!", "error");
        return;
    }

    try {
        const formData = new FormData();
        formData.append("name", username);
        formData.append("cnic", cnic);
        formData.append("email", email);
        formData.append("password", password);
        formData.append("cnic_image", cnicImageInput.files[0]);
        formData.append("live_image", window.liveImageBase64);

        console.log("Sending FormData:");
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ', pair[1]);
        }

        const response = await fetch("http://127.0.0.1:8000/api/register", {
            method: "POST",
            body: formData
        });

        const data = await response.json();
        console.log("Backend response:", data);

        if (response.status === 422 && data.error) {
            let errors = [];
            for (let field in data.error) {
                errors.push(...data.error[field]);
            }
            showMessage(errors.join("<br>"), "error");
            return;
        }

        if (response.ok) {
            showMessage(data.message || "Registration successful!", "success");
            document.getElementById("registerForm").reset();
            document.getElementById('live_image_preview').src = '';
            return;
        }

        showMessage(data.message || "Something went wrong!", "error");

    } catch (error) {
        console.error("Error:", error);
        showMessage("Server error, please try again!", "error");
    }
});

// Helper function to show messages
function showMessage(text, type) {
    const messageBox = document.getElementById("formMessage");
    messageBox.innerHTML = text;
    messageBox.className = `p-3 mb-4 text-center rounded ${type === 'error' ? 'bg-red-500' : 'bg-green-500'} text-white`;
    messageBox.classList.remove("hidden");
}

// Password toggle function
function togglePassword() {
    const passwordInput = document.getElementById("password");
    const passwordToggle = document.getElementById("passwordToggle");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        passwordToggle.classList.remove("fa-eye");
        passwordToggle.classList.add("fa-eye-slash");
    } else {
        passwordInput.type = "password";
        passwordToggle.classList.remove("fa-eye-slash");
        passwordToggle.classList.add("fa-eye");
    }
}

// ------------------------------------------------------------------

document.getElementById("email").addEventListener("change", async function () {
    const email = this.value;

    const response = await fetch("http://127.0.0.1:8000/api/send-otp", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email })
    });

    const data = await response.json();
    alert(data.message);

    document.getElementById("otpBox").classList.remove("hidden");
});

// -------------------------------------------------------------------------------------------

document.getElementById("verifyOtpBtn").addEventListener("click", async function () {
    const email = document.getElementById("email").value;
    const otp = document.getElementById("otp").value;

    const response = await fetch("http://127.0.0.1:8000/api/verify-otp", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, otp })
    });

    const data = await response.json();

    if (response.ok) {
        alert("OTP Verified!");
        window.emailVerified = true;
    } else {
        alert(data.error);
    }
});

// ----------------------------------------------------------------------------------------------------

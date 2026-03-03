
document.getElementById("addAdminForm").addEventListener("submit", async function(e) {
    e.preventDefault();

    // Clear previous messages
    const messageBox = document.getElementById("formMessage");
    messageBox.classList.add("hidden");
    messageBox.textContent = "";

    // Collect form values
    const data = {
    name: document.getElementById("username").value, // map username → name
    email: document.getElementById("email").value,
    password: document.getElementById("password").value,
};


    try {
        const response = await fetch("http://127.0.0.1:8000/api/create-admin", {  // 👈 replace with your API URL
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify(data)
        });

        const result = await response.text(); // since your API returns plain text messages
       
        messageBox.textContent = result;
        messageBox.classList.remove("hidden");

        if (response.ok) {
            messageBox.className = "text-green-400 text-center mb-4"; // ✅ success
            document.getElementById("addAdminForm").reset(); // clear form
        } else {
            messageBox.className = "text-red-400 text-center mb-4";   // ❌ validation error
        }
    } catch (error) {
        messageBox.textContent = "Something went wrong. Please try again.";
        messageBox.className = "text-red-400 text-center mb-4";
        messageBox.classList.remove("hidden");
    }
});


// Copy this script into Postman -> Login request -> Tests tab
// This script will automatically save the token to admin_token environment variable

if (pm.response.code === 200) {
    try {
        var jsonData = pm.response.json();
        var token = null;
        var user = null;
        
        // Try nested structure (data.token) - Standard API response
        if (jsonData.data && jsonData.data.token) {
            token = jsonData.data.token;
            user = jsonData.data.user;
        }
        // Try root structure (token) - Alternative response format
        else if (jsonData.token) {
            token = jsonData.token;
            user = jsonData.user;
        }
        
        if (token) {
            pm.environment.set("admin_token", token);
            pm.environment.set("auth_token", token); // Also set for backward compatibility
            
            if (user && user.id) {
                pm.environment.set("user_id", user.id);
            }
            if (user && user.national_id) {
                pm.environment.set("national_id", user.national_id);
            }
            
            console.log("Token saved successfully!");
        } else {
            console.log("Token not found in response");
        }
    } catch (e) {
        console.log("Error parsing response:", e);
    }
} else {
    console.log("Login failed with status:", pm.response.code);
}


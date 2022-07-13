<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

    <div>
        <h1>Test Fire base</h1>
    </div>

    <script type="module">
        // Import the functions you need from the SDKs you need
        import { initializeApp } from "https://www.gstatic.com/firebasejs/9.9.0/firebase-app.js";
        import { getAnalytics } from "https://www.gstatic.com/firebasejs/9.9.0/firebase-analytics.js";
        import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/9.9.0/firebase-messaging.js";

        // TODO: Add SDKs for Firebase products that you want to use
        // https://firebase.google.com/docs/web/setup#available-libraries

        // Your web app's Firebase configuration
        // For Firebase JS SDK v7.20.0 and later, measurementId is optional
        const firebaseConfig = {
          apiKey: "AIzaSyCiTsmIflEuFPJ9cPRX255VQ65iqN0gsAI",
          authDomain: "new-project-fc150.firebaseapp.com",
          projectId: "new-project-fc150",
          storageBucket: "new-project-fc150.appspot.com",
          messagingSenderId: "927251905826",
          appId: "1:927251905826:web:21f43ae4349a5e8849f97b",
          measurementId: "G-8SSM07VS2W"
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        // const analytics = getAnalytics(app);
        // Initialize Firebase Cloud Messaging and get a reference to the service
        const messaging = getMessaging(app);
        const response = await getToken(messaging, { vapidKey: "BAvMnSSbXOTZTWxQ8Ui_Kvd8hksl0W_YNDWOFlXCaY0-8xGj3W1Lz2Ufj6X1yiulJsrQwuIQDfEl643yMmWyvrc" })
        // .then((currentToken) => {
        //     if (currentToken) {
        //         // Send the token to your server and update the UI if necessary
        //         // ...
        //     } else {
        //         // Show permission request UI
        //         console.log('No registration token available. Request permission to generate one.');
        //         // ...
        //     }
        // }).catch((err) => {
        //     console.log(err);
        // });

        console.log(response)

        async function requestPermission() {
            console.log('Requesting permission...');

            Notification.requestPermission().then((permission) => {
                if (permission === 'granted') {
                    const item = response()
                    console.log(item)
                    console.log('Notification permission granted.');
                }
            })
        }


      </script>
</body>
</html>

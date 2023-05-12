<!DOCTYPE html>
<html>
   <head>
      <title>DuaCentral</title>
      <link rel="stylesheet" href="styles.css">
      <style>
         .sidebar {
         background-color: #f1f1f1;
         width: 250px;
         position: absolute;
         top: 10; /* Add this line */
         bottom: 0px;
         left: 0;
         height: calc(100% - 80px); /* Add this line */
         overflow-y: auto; /* Add this line */
         padding: 8px 0;
         }
         .sidebar ul {
         list-style-type: none;
         padding: 0;
         margin: 0;
         }
         .sidebar li {
         padding: 8px 16px;
         cursor: pointer;
         }
         .sidebar li:hover {
         background-color: #f1f1f1;
         }
         .sidebar::-webkit-scrollbar {
         width: 0.5em; /* Set width of the scrollbar */
         }
         .sidebar::-webkit-scrollbar-thumb {
         background-color: #888; /* Set color of the thumb */
         }
         .sidebar::-webkit-scrollbar-track {
         background-color: #eee; /* Set color of the track */
         }
      </style>
      <script src="https://www.gstatic.com/firebasejs/9.6.8/firebase-app-compat.js"></script>
      <script src="https://www.gstatic.com/firebasejs/9.6.8/firebase-auth-compat.js"></script>
      <script src="https://www.gstatic.com/firebasejs/9.6.8/firebase-firestore-compat.js"></script>
      <script>
         const quotes = [
             "“Do not lose hope, nor be sad.” Quran 3:139",
             "“Never underestimate the power of Dua (supplication).” Anonymous",
             "“To Allah (God) is your return, all of you, and He will inform you of what you used to do.” Quran 5:105",
             "“Whoever fears Allah (God), Allah (God) will find a way out for him (from every difficulty) and He will provide for him from sources that he could never have imagined.” Quran 65:2-3",
             "“The life of this world is merely enjoyment of delusion.” Quran 3:185",
             "“And whoever turns away from My remembrance – indeed, he will have a depressed life.” Quran 20:124",
             "“Allah (God) does not burden a soul beyond that it can bear.” Quran 2:286",
             "“For indeed, with hardship [will be] ease.” Quran 94:5",
             "“Every soul will taste death. Then unto Us will you be returned.” Quran 29:57",
             "“And whoever holds firmly to Allah (God) has (indeed) been guided to a straight path.” Quran 3:101",
             "“Before going to sleep every night, forgive everyone and sleep with a clean heart.” Anonymous",
             "“Dua (supplication) has the power to turn your dreams into reality.” Anonymous",
             "“Once prayer becomes a habit, success becomes a lifestyle.” Anonymous"
         ];
         
         const firebaseConfig = {
         apiKey: process.env.FIREBASE_API_KEY,
         authDomain: process.env.FIREBASE_AUTH_DOMAIN,
         projectId: process.env.FIREBASE_PROJECT_ID,
         storageBucket: process.env.FIREBASE_STORAGE_BUCKET,
         messagingSenderId: process.env.FIREBASE_MESSAGING_SENDER_ID,
         appId: process.env.FIREBASE_APP_ID
         };
         
         firebase.initializeApp(firebaseConfig);
         
         firebase.auth().onAuthStateChanged((user) => {
         const loginItem = document.getElementById("loginItem");
         const accountItem = document.getElementById("accountItem");
         const favoritesLink = document.getElementById("favoritesLink");
         
         if (user) {
         // User is signed in
         const userId = user.uid;
         const db = firebase.firestore();
         const userRef = db.collection("users").doc(userId);
         
         // Check if the user document already exists
         userRef.get().then((doc) => {
         if (!doc.exists) {
         // If the user document doesn't exist, create it
         userRef.set({}).then(() => {
           console.log("User document created.");
         }).catch((error) => {
           console.error("Error creating user document:", error);
         });
         }
         });
         
         accountItem.style.display = "inline";
         loginItem.style.display = "none";
         favoritesLink.href = "favorites.html"; // Set the Favorites link for signed-in users
         
         } else {
         // User is signed out
         accountItem.style.display = "none";
         loginItem.style.display = "inline";
         favoritesLink.href = "login.html"; // Set the Favorites link for signed-out users
         }
         
         // Always show the Favorites link
         favoritesLink.style.display = "inline";
         });
         
         
         document.addEventListener("DOMContentLoaded", () => {
             const signOutLink = document.getElementById("signOutLink");
             signOutLink.onclick = () => {
                 firebase.auth().signOut().then(() => {
                     // Sign-out successful
                     window.location.reload();
                 }).catch((error) => {
                     // An error occurred
                     console.error('Error signing out:', error);
                 });
             };
         });
         
         
         function fetchDuas() {
         const duaList = document.getElementById("duaList");
         const db = firebase.firestore();
         db.collection("duas").get().then((querySnapshot) => {
         querySnapshot.forEach((doc) => {
             const duaData = doc.data();
             const listItem = document.createElement("li");
             listItem.setAttribute("data-id", doc.id);
             listItem.textContent = duaData.Title.trim();
             listItem.onclick = () => {
                 const selectedDuaId = listItem.getAttribute("data-id");
                 showDuaInfo(selectedDuaId);
             };
             duaList.appendChild(listItem);
         });
         });
         }
         
         
         
         
         // Call the fetchDuas function when the page is loaded
         document.addEventListener("DOMContentLoaded", fetchDuas);
         
         function showDuaInfo(selectedDuaId) {
         const quoteElement = document.getElementById("quoteOfTheDay");
         const duaInfoElement = document.getElementById("duaInfo");
         if (!selectedDuaId) {
         quoteElement.classList.remove("hidden");
         duaInfoElement.classList.add("hidden");
         return;
         } else {
         quoteElement.classList.add("hidden");
         duaInfoElement.classList.remove("hidden");
         }
         const db = firebase.firestore();
         db.collection("duas").doc(selectedDuaId).get().then((doc) => {
         if (doc.exists) {
             const duaData = doc.data();
             document.getElementById("duaTitle").textContent = duaData.Title;
             document.getElementById("duaDescription").textContent = duaData.Description;
             document.getElementById("duaArabic").parentElement.removeAttribute("hidden");
             document.getElementById("duaArabic").textContent = duaData.Arabic;
             document.getElementById("duaTransliteration").parentElement.removeAttribute("hidden");
             document.getElementById("duaTransliteration").textContent = duaData.Transliteration;
             document.getElementById("duaMeaning").parentElement.removeAttribute("hidden");
             document.getElementById("duaMeaning").textContent = duaData.Meaning;
         } else {
             console.log("No such document!");
         }
         }).catch((error) => {
         console.log("Error getting document:", error);
         });
         
         const userId = firebase.auth().currentUser.uid;
         const userRef = db.collection("users").doc(userId);
         const favoriteButton = document.getElementById("favoriteButton");
         
         userRef.get().then((doc) => {
         if (doc.exists) {
             const userData = doc.data();
             const favorites = userData.favorites || [];
         
             if (favorites.includes(selectedDuaId)) {
                 favoriteButton.classList.add("red");
             } else {
                 favoriteButton.classList.remove("red");
             }
         }
         });
         }
         
         
         function getQuoteOfTheDay() {
             const storedQuote = localStorage.getItem("quoteOfTheDay");
             const storedTimestamp = localStorage.getItem("quoteTimestamp");
             const currentTime = new Date().getTime();
             const oneDay = 24 * 60 * 60 * 1000;
             if (storedQuote && storedTimestamp && currentTime - storedTimestamp < oneDay) {
                 return storedQuote;
             } else {
                 const randomIndex = Math.floor(Math.random() * quotes.length);
                 const newQuote = quotes[randomIndex];
                 localStorage.setItem("quoteOfTheDay", newQuote);
                 localStorage.setItem("quoteTimestamp", currentTime);
                 return newQuote;
             }
         }
         document.addEventListener("DOMContentLoaded", () => {
             document.getElementById("quoteOfTheDay").textContent = getQuoteOfTheDay();
             const favoritesLink = document.getElementById("favoritesLink");
         
             showDuaInfo();
         });
         
         
         async function toggleFavorite() {
         const userId = firebase.auth().currentUser?.uid;
         if (!userId) {
         const messageElement = document.getElementById("loginMessage");
         messageElement.classList.add("visible");
         setTimeout(() => {
         messageElement.classList.remove("visible");
         }, 3000);
         return;
         }
         
         const selectedDuaId = document.querySelector('#duaList li[data-id]').getAttribute('data-id');
         const db = firebase.firestore();
         const userRef = db.collection("users").doc(userId);
         const favoriteButton = document.getElementById("favoriteButton");
         
         const doc = await userRef.get();
         if (doc.exists) {
         const userData = doc.data();
         const favorites = userData.favorites || [];
         
         if (favorites.includes(selectedDuaId)) {
         // Remove the selected dua from favorites
         const index = favorites.indexOf(selectedDuaId);
         favorites.splice(index, 1);
         favoriteButton.classList.remove("red");
         } else {
         // Add the selected dua to favorites
         favorites.push(selectedDuaId);
         favoriteButton.classList.add("red");
         }
         
         // Update the favorites array in Firestore
         await userRef.update({ favorites }).catch((error) => {
         console.error("Error updating favorites:", error);
         });
         }
         }
      </script>
   </head>
   <body>
      <header>
         <h1 style="text-align: left; margin-left: 20px;">DuaCentral</h1>
         <nav class="top-right">
            <ul>
               <br>
               <li><a href="index.php">Home</a></li>
               <li><a href="about.html">About</a></li>
               <li><a href="contact.html">Contact</a></li>
               <li><a href="favorites.html" id="favoritesLink" style="display:none;">Favorites</a></li>
               <li id="loginItem"><a href="login.html" id="loginLink">Log In</a></li>
               <li id="accountItem" style="display:none;">
                  <span id="accountLink" class="accountLink-heading">My Account</span>
                  <div id="accountDropdown" class="accountLink">
                     <a href="#" id="signOutLink">Logout</a>
                  </div>
               </li>
            </ul>
         </nav>
      </header>
      <div class="container">
         <main class="content">
            <h2 class="arabic-text">بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ</h2>
            <h2>Welcome to DuaCentral</h2>
            <p>Find and save your favourite duas.</p>
            <div class="sidebar">
               <ul id="duaList">
                  <li><strong>Select a Dua</strong></li>
               </ul>
            </div>
            <div id="duaInfo" class="hidden">
               <h3 id="duaTitle"></h3>
               <p id="duaDescription"></p>
               <p hidden><strong>Arabic:</strong> <span id="duaArabic" style="font-size: 2em;"></span></p>
               <p hidden><strong>Transliteration:</strong> <span id="duaTransliteration"></span></p>
               <p hidden><strong>Meaning:</strong> <span id="duaMeaning"></span></p>
               <span id="favoriteButton" class="heart" onclick="toggleFavorite()">&#9829;</span>
               <p id="loginMessage" class="login-message">Please log in or create an account to save duas to your favorites.</p>
            </div>
            <p id="quoteOfTheDay" class="hidden"></p>
         </main>
      </div>
      <nav class="bottom">
         <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="about.html">About</a></li>
            <li><a href="contact.html">Contact</a></li>
            <li><a href="login.html">Log In</a></li>
         </ul>
      </nav>
      <footer>
         <p>&copy; 2023 DuaCentral. All rights reserved.</p>
      </footer>
   </body>
</html>
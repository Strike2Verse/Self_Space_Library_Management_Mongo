
# <img src="https://media2.giphy.com/media/kDjypgTGS3WLyrE6FL/giphy.gif?cid=6c09b952ytaslfx9twte5v4issdjonhgnj3hg319qtiscojs&ep=v1_internal_gif_by_id&rid=giphy.gif&ct=s" alt="GIF" width="50" height="auto" /> Self_Spaced_Library_Management_Mongodb <img src="https://clipart-library.com/images/yikK8kkrT.gif" alt="GIF" width="50" height="auto" />

A convenient library management system allowing users to register, browse books, and manage their personal reading space.  Administrators can manage the book collection and oversee the platform.


## About Project
**Self-Spaced Library Management** is a personalized library system where users can manage their own book collection in a secure, private space. The project includes features for user and admin roles, allowing users to create accounts, add books to their collection, and track what they’ve read. Admins have additional privileges, such as managing the entire book collection, adding new books, and removing outdated ones. The system also includes basic registration and login functionality.

Although the project is functional, it currently lacks features like "Forgot Password" functionality and social media login options (e.g., Google, Twitter, GitHub). These features are planned for future updates.

This project aims to provide users with an easy-to-use platform to manage their reading habits, while offering admins control over the book collection database.


## Table of Contents

* [Features](#features)
* [Technologies Used](#technologies-used)
* [Installation](#installation)


## Features

* **User Registration and Login:** Users can create accounts and securely log in.
* **Personalized Reading Space:** Each user has a dedicated space to add and access their selected books.
* **Book Browsing and Filtering:** Users can browse the available books and filter by criteria.
* **Book Searching:**  Users can search for specific books.
* **PDF Reading:** Users can read books directly within the platform in PDF format.
* **Admin Panel:** Administrators can add, delete, and manage the book collection.


## Technologies Used

* HTML
* CSS
* PHP
* JavaScript (partially)
* MongoDB (database)


## Installation

1. **Clone the Repository:**  `git clone https://github.com/Strike2Verse/Self_Spaced_Library_Management_Mongodb.git`
2. **Install Composer:**  Ensure you have Composer installed on your system.  If not, download and install it from [https://getcomposer.org/](https://getcomposer.org/).  Composer is required for managing MongoDB dependencies.

   ```
   /Your_Project_Root_Directory
        │
        ├── composer.json           <-- Composer file
        ├── composer.lock           <-- Composer lock file
        ├── vendor/                 <-- Composer's dependencies folder
        │
        ├── /project-folder         <-- Main project folder with source files (Your Folder)
        │   ├── file_1           <-- Example source file
        └── other_project_files/
   ```
      
3. **MongoDB Compass:** Make sure you have MongoDB Compass installed and running. Download it from [https://www.mongodb.com/products/compass](https://www.mongodb.com/try/download/compass).  This is the database management tool used by the project.
4. **Database Setup (Important):**
  *  Import the required database files into your MongoDB Compass instance. *you can import collection in compass by seeing the code you will see the collection names*.  
  * To configure the database connection for this project, locate the line in the code where the MongoDB client is initialized. Look for ***$client = new MongoDB\Client("URL_TO_YOUR_MONGODB_SERVER");*** and replace "URL_TO_YOUR_MONGODB_SERVER" with your actual MongoDB connection string. For example, if you're using a local MongoDB instance, you can useURL you can find from mongodb compass as the connection URL. After updating the URL, save the file to apply the new connection settings. This change will ensure the application is correctly connected to your MongoDB server.
5. **Run the Project Locally:**
  - At last, for running the project, simply open your project folder in **VS Code**.
  - Install the **PHP Server** extension if you haven't already.
  - Open the `home.html` file and either use the **Run with PHP Server** option or launch the project using the **PHP Server** extension.
  - This will start a local server, and you can then open your browser to view and interact with the project.


<h1 align="center">Thank you for taking the time to explore this project! </h1>

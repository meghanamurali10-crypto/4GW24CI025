# Lab Book Automation System

## Overview
The **Lab Book Automation System** is a Java-based console application that simplifies the process of tracking students' computer lab usage. It allows students to *log their entry and exit times* by scanning their ID (USN), while administrators can view both current active sessions and full session histories.

This project demonstrates practical use of **object-oriented programming**, **collections**, and **Java time APIs**.

***

##  Features

- **Student Check-in/Check-out:**  
  Students scan their ID (USN) to mark entry or exit. The system automatically records timestamps.

- **Automatic Duration Calculation:**  
  The program computes total usage time between entry and exit, displayed in minutes and seconds.

- **Admin Dashboard:**  
  View:
  - All currently active students (system occupancy).
  - Full lab usage history (start, end, and duration of sessions).

- **Persistent Console Interface:**  
  Simple, interactive, menu-driven navigation for students and admins.

***
** Team Members**

Meghana M - 4GW24CI025
Anjali Ajith - 4GW24CI003

***
## 🧩 Project Structure

```
java_assisgnment_lab_book_assignment/
├── LabBookAutomation.java   # Main application file
└── Student.java             # Student model class
```

### Key Components

- **`LabBookAutomation`**
  - Handles main logic, user prompts, and application menus.
  - Manages list of `Student` sessions.
  - Performs entry validation, timestamp recording, and session reporting.

- **`Student`**
  - Represents individual student lab records.
  - Stores name, ID, system number, start time, and end time.

***

##  How to Run

1. **Clone or Copy the Project**
   ```bash
   git clone https://github.com/anjaliajithofficial-dev/4GW24CI003.git
   cd lab-book-automation
   ```

2. **Compile the Java Files**
   ```bash
   javac java_assisgnment_lab_book_assignment/*.java
   ```

3. **Run the Program**
   ```bash
   java java_assisgnment_lab_book_assignment.LabBookAutomation
   ```

4. **Follow the On-Screen Menu**
   ```
   --- Lab Book Automation System ---

   ** Main Menu **
   1. Student Scan (Entry/Exit)
   2. Admin View
   3. Exit Program
   ```

***

##  Sample Workflow

### Student Section
1. Choose option **1. Student Scan (Entry/Exit)**.
2. Enter your **USN**.
3. If you are a new user, enter your name and system number.
4. The system marks your **entry** with the current timestamp.
5. When scanning again, it marks your **exit**, showing the total usage duration.

### Admin Section
1. Choose option **2. Admin View**.
2. Select:
   - **1** to view all currently active students.
   - **2** to view the full session history.

***

##  Example Output (Simplified)

```
--- Lab Book Automation System ---

** Main Menu **
1. Student Scan (Entry/Exit)
2. Admin View
3. Exit Program
Enter your choice: 1

--- Student Scan Terminal ---
Please 'scan' your USN/ID: 4GW24CI025
New student detected. Enter your full name: Meghana M
Enter the system number you are using: 5

 Entry added successfully!
Start Time: 10:15:24
```

Later, scanning the same ID marks exit:
```
 Clock Out marked successfully for Priya Sharma
End Time: 12:30:42
---

***

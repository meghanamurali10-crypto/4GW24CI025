#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <stdbool.h>

// --- Data Structure (Linked List) ---
struct Student
{
    char name[50];
    char id[20];
    int systemNo;
    struct tm startTime;
    struct tm endTime;
    struct Student *next;
};

struct Student *head = NULL;

// --- Utility Functions ---

// Function to clear input buffer (Handles non-integer input errors gracefully)
void clear_buffer() {
    int c;
    while ((c = getchar()) != '\n' && c != EOF);
}

// Function to find an ACTIVE (no end time) entry by ID, returns the node or NULL
struct Student* findActiveEntry(const char *id) {
    struct Student *temp = head;
    while (temp != NULL) {
        // Check ID match AND if endTime's year is the default 0 (unmarked/active)
        if (strcmp(temp->id, id) == 0 && temp->endTime.tm_year == 0) {
            return temp;
        }
        temp = temp->next;
    }
    return NULL;
}

// Function to find ANY entry (active or exited) by ID, returns the node or NULL
struct Student* findAnyEntry(const char *id) {
    struct Student *temp = head;
    while (temp != NULL) {
        if (strcmp(temp->id, id) == 0) {
            return temp;
        }
        temp = temp->next;
    }
    return NULL;
}

// --- 1. Student Scan Logic (Automated Entry/Exit) ---

void handleScan() {
    char usn[20];
    printf("\n--- Student Scan Terminal ---\n");
    printf("Please 'scan' your USN/ID: ");
    if (scanf(" %[^\n]", usn) != 1) {
        printf("Invalid input.\n");
        clear_buffer();
        return;
    }

    struct Student *activeEntry = findActiveEntry(usn);

    // 1. Mark Exit if an active entry is found
    if (activeEntry != NULL) {
        time_t now = time(NULL);
        activeEntry->endTime = *localtime(&now);
        printf("\n✅ Clock Out marked successfully for **%s**.\n", activeEntry->name);
        printf("End Time: %s", asctime(&activeEntry->endTime));
        return;
    }

    // 2. Mark Entry if no active entry is found
    struct Student *newStudent = (struct Student *)malloc(sizeof(struct Student));
    if (newStudent == NULL) {
        printf("Memory allocation failed!\n");
        return;
    }
    
    // Find previous entry to get name if possible
    struct Student *existingStudent = findAnyEntry(usn);
    
    // Copy ID/USN
    strcpy(newStudent->id, usn);

    if (existingStudent != NULL) {
        // Use existing name
        strcpy(newStudent->name, existingStudent->name); 
        printf("Welcome back, **%s**! (USN: %s)\n", newStudent->name, usn);
    } else {
        // New student - prompt for name
        printf("New student detected. Enter your full name: ");
        clear_buffer(); // Ensure buffer is clear before reading name
        if (scanf(" %[^\n]", newStudent->name) != 1) {
            printf("Invalid name input. Entry aborted.\n");
            free(newStudent);
            return;
        }
    }

    printf("Enter the system number you are using: ");
    if (scanf("%d", &newStudent->systemNo) != 1) {
        printf("Invalid system number input. Entry aborted.\n");
        clear_buffer();
        free(newStudent);
        return;
    }

    // Capture start time automatically
    time_t now = time(NULL);
    newStudent->startTime = *localtime(&now);
    newStudent->endTime.tm_year = 0; // Explicitly mark as active
    newStudent->next = NULL;

    // Add to linked list (at the end)
    if (head == NULL) {
        head = newStudent;
    } else {
        struct Student *temp = head;
        while (temp->next != NULL) {
            temp = temp->next;
        }
        temp->next = newStudent;
    }

    printf("\n✅ Entry added successfully!\n");
    printf("Start Time: %s", asctime(&newStudent->startTime));
}

// --- 2. Admin View Functions ---

// Displays only students who are currently logged in (active)
void displayActiveEntries() {
    struct Student *temp = head;
    int activeCount = 0;

    printf("\n--- Currently Active Students (System Occupancy) ---\n");
    printf("Name\t\tID\t\tSystem No\tStart Time\t\tDuration\n");
    printf("--------------------------------------------------------------------------------\n");

    while (temp != NULL) {
        // Check if endTime year is 0, indicating an active session
        if (temp->endTime.tm_year == 0) {
            activeCount++;
            char startStr[9]; // For HH:MM:SS
            strftime(startStr, sizeof(startStr), "%H:%M:%S", &temp->startTime);

            time_t now = time(NULL);
            double duration = difftime(now, mktime(&temp->startTime));

            printf("%s\t\t%s\t\t%d\t\t%s\t\t%.0f min (%.0f sec)\n",
                   temp->name, temp->id, temp->systemNo,
                   startStr, duration / 60.0, duration);
        }
        temp = temp->next;
    }

    if (activeCount == 0) {
        printf("--- No students currently active in the lab. ---\n");
    } else {
        printf("--------------------------------------------------------------------------------\n");
        printf("Total Active Systems Occupied: **%d**\n", activeCount);
    }
}

// Displays all entries (active and exited)
void displayAllEntries() {
    struct Student *temp = head;
    if (temp == NULL) {
        printf("\nNo entries found!\n");
        return;
    }
    printf("\n--- Full Lab Book History ---\n");
    printf("Name\t\tID\t\tSystem No\tStart Time\t\tEnd Time\t\tDuration\n");
    printf("------------------------------------------------------------------------------------------------------\n");

    while (temp != NULL) {
        char startStr[9], endStr[9];
        strftime(startStr, sizeof(startStr), "%H:%M:%S", &temp->startTime);

        if (temp->endTime.tm_year != 0) {
            // Exited entry
            strftime(endStr, sizeof(endStr), "%H:%M:%S", &temp->endTime);
            double duration = difftime(mktime(&temp->endTime), mktime(&temp->startTime));
            printf("%s\t\t%s\t\t%d\t\t%s\t\t%s\t\t%.0f min\n",
                   temp->name, temp->id, temp->systemNo, startStr, endStr, duration / 60.0);
        } else {
            // Active entry
            printf("%s\t\t%s\t\t%d\t\t%s\t\t---\t\t---\n",
                   temp->name, temp->id, temp->systemNo, startStr);
        }
        temp = temp->next;
    }
    printf("\n");
}

// --- Main Menu Logic ---

void adminMenu() {
    int choice;
    do {
        printf("\n== Admin Menu ==\n");
        printf("1. View Active Students (Occupancy)\n");
        printf("2. View Full Lab Book History\n");
        printf("3. Back to Main Menu\n");
        printf("Enter your choice: ");
        if (scanf("%d", &choice) != 1) {
            printf("Invalid input. Try again.\n");
            clear_buffer();
            choice = 0; // Force loop continuation
        }
        
        switch (choice) {
            case 1:
                displayActiveEntries();
                break;
            case 2:
                displayAllEntries();
                break;
            case 3:
                printf("Returning to Main Menu...\n");
                break;
            default:
                if (choice != 0) printf("Invalid choice! Try again.\n");
        }
    } while (choice != 3);
}


int main() {
    int choice;
    printf("--- Lab Book Automation System ---\n");

    while (true) {
        printf("\n** Main Menu **\n");
        printf("1. Student Scan (Entry/Exit)\n");
        printf("2. Admin View\n");
        printf("3. Exit Program\n");
        printf("Enter your choice: ");
        if (scanf("%d", &choice) != 1) {
            printf("Invalid input. Please enter a number.\n");
            clear_buffer();
            choice = 0;
        }

        switch (choice) {
            case 1:
                handleScan();
                break;
            case 2:
                adminMenu();
                break;
            case 3:
                printf("System shutting down. Goodbye!\n");
                // TODO: Add code here to free all allocated memory (the linked list)
                return 0;
            default:
                if (choice != 0) printf("Invalid choice! Try again.\n");
        }
    }
    return 0;
}
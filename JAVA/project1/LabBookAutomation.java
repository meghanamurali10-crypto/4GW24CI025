package java_assisgnment_lab_book_assignment;
import java.time.Duration;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.*;
public class LabBookAutomation {

    private static final Scanner sc = new Scanner(System.in);
    private static final List<Student> studentList = new ArrayList<>();
    private static final DateTimeFormatter timeFormat = DateTimeFormatter.ofPattern("HH:mm:ss");

    // Utility - find active entry
    private static Student findActiveEntry(String id) {
        for (Student s : studentList) {
            if (s.id.equals(id) && s.endTime == null)
                return s;
        }
        return null;
    }

    // Utility - find any (previous) entry
    private static Student findAnyEntry(String id) {
        for (Student s : studentList) {
            if (s.id.equals(id))
                return s;
        }
        return null;
    }

    // --- 1. Student Scan Logic (Entry / Exit) ---
    private static void handleScan() {
        System.out.println("\n--- Student Scan Terminal ---");
        System.out.print("Please 'scan' your USN/ID: ");
        String usn = sc.nextLine().trim();

        if (usn.isEmpty()) {
            System.out.println("Invalid input.");
            return;
        }

        Student active = findActiveEntry(usn);

        // If active entry found → mark exit
        if (active != null) {
            active.endTime = LocalDateTime.now();
            System.out.println("\n✅ Clock Out marked successfully for **" + active.name + "**");
            System.out.println("End Time: " + active.endTime.format(DateTimeFormatter.ofPattern("HH:mm:ss")));
            return;
        }

        // Otherwise, new entry
        Student existing = findAnyEntry(usn);
        String name;

        if (existing != null) {
            name = existing.name;
            System.out.println("Welcome back, **" + name + "**! (USN: " + usn + ")");
        } else {
            System.out.print("New student detected. Enter your full name: ");
            name = sc.nextLine().trim();
            if (name.isEmpty()) {
                System.out.println("Invalid name input. Entry aborted.");
                return;
            }
        }

        System.out.print("Enter the system number you are using: ");
        int sys;
        try {
            sys = Integer.parseInt(sc.nextLine());
        } catch (NumberFormatException e) {
            System.out.println("Invalid system number input. Entry aborted.");
            return;
        }

        Student student = new Student(name, usn, sys, LocalDateTime.now());
        studentList.add(student);

        System.out.println("\n✅ Entry added successfully!");
        System.out.println("Start Time: " + student.startTime.format(timeFormat));
    }

    // --- 2. Admin View Functions ---
    private static void displayActiveEntries() {
        System.out.println("\n--- Currently Active Students (System Occupancy) ---");
        System.out.printf("%-15s %-15s %-10s %-12s %-15s%n",
                "Name", "ID", "System No", "Start Time", "Duration");

        int count = 0;
        for (Student s : studentList) {
            if (s.endTime == null) {
                count++;
                Duration d = Duration.between(s.startTime, LocalDateTime.now());
                System.out.printf("%-15s %-15s %-10d %-12s %.0f min (%.0f sec)%n",
                        s.name, s.id, s.systemNo, s.startTime.format(timeFormat),
                        (double)d.toMinutes(), (double)d.toSeconds());
            }
        }

        if (count == 0)
            System.out.println("--- No students currently active in the lab. ---");
        else
            System.out.println("Total Active Systems Occupied: " + count);
    }

    private static void displayAllEntries() {
        if (studentList.isEmpty()) {
            System.out.println("\nNo entries found!");
            return;
        }

        System.out.println("\n--- Full Lab Book History ---");
        System.out.printf("%-15s %-15s %-10s %-12s %-12s %-8s%n", 
                "Name", "ID", "System No", "Start Time", "End Time", "Duration(min)");

        for (Student s : studentList) {
            String start = s.startTime.format(timeFormat);
            String end = (s.endTime != null) ? s.endTime.format(timeFormat) : "---";
            double duration = (s.endTime != null) 
                    ? Duration.between(s.startTime, s.endTime).toMinutes() 
                    : 0;

            System.out.printf("%-15s %-15s %-10d %-12s %-12s %-8.0f%n",
                    s.name, s.id, s.systemNo, start, end, duration);
        }
    }

    // --- Admin Menu Logic ---
    private static void adminMenu() {
        int choice;
        do {
            System.out.println("\n== Admin Menu ==");
            System.out.println("1. View Active Students (Occupancy)");
            System.out.println("2. View Full Lab Book History");
            System.out.println("3. Back to Main Menu");
            System.out.print("Enter your choice: ");

            try {
                choice = Integer.parseInt(sc.nextLine());
            } catch (NumberFormatException e) {
                System.out.println("Invalid input. Try again.");
                choice = 0;
            }

            switch (choice) {
                case 1 -> displayActiveEntries();
                case 2 -> displayAllEntries();
                case 3 -> System.out.println("Returning to Main Menu...");
                default -> {
                    if (choice != 0) System.out.println("Invalid choice! Try again.");
                }
            }
        } while (choice != 3);
    }

    // --- Main Method ---
    public static void main(String[] args) {
        System.out.println("--- Lab Book Automation System ---");

        while (true) {
            System.out.println("\n** Main Menu **");
            System.out.println("1. Student Scan (Entry/Exit)");
            System.out.println("2. Admin View");
            System.out.println("3. Exit Program");
            System.out.print("Enter your choice: ");

            int choice;
            try {
                choice = Integer.parseInt(sc.nextLine());
            } catch (NumberFormatException e) {
                System.out.println("Invalid input. Please enter a number.");
                continue;
            }

            switch (choice) {
                case 1 -> handleScan();
                case 2 -> adminMenu();
                case 3 -> {
                    System.out.println("System shutting down. Goodbye!");
                    return;
                }
                default -> System.out.println("Invalid choice! Try again.");
            }
        }
    }
}


package java_assisgnment_lab_book_assignment;
import java.time.LocalDateTime;

class Student {
    String name;
    String id;
    int systemNo;
    LocalDateTime startTime;
    LocalDateTime endTime; // null = active

    Student next; // Optional if you want to mimic linked list manually

    Student(String name, String id, int systemNo, LocalDateTime startTime) {
        this.name = name;
        this.id = id;
        this.systemNo = systemNo;
        this.startTime = startTime;
        this.endTime = null;
    }
}
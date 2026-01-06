package program3;
import java.util.Scanner;
public class EmployeeDemo {

	public static void main(String[] args) {
		Scanner sc = new Scanner(System.in); 
		
		System.out.print("Enter Employee ID: "); 
		int id = sc.nextInt(); 
		sc.nextLine(); 
		
		System.out.print("Enter Employee Name: "); 
		String name = sc.nextLine(); 
		
		System.out.print("Enter Employee Salary: "); 
		double salary = sc.nextDouble(); 
		
		Employee emp = new Employee(id, name, salary); 
		
		System.out.println("\n--- Employee Details Before Salary Raise ---"); 
		emp.display(); 
	
		System.out.print("\nEnter percentage to raise salary: "); 
		double percent = sc.nextDouble(); 
		emp.raiseSalary(percent); 
		System.out.println("\n--- Employee Details After Salary Raise ---"); 
		emp.display(); 
		sc.close(); 
		
		// TODO Auto-generated method stub

	}

}

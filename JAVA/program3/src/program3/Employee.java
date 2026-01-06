package program3;

public class Employee {
	private int id;
	private String name; 
	private double salary;	
	public Employee(int id, String name, double salary) { 
		this.id = id; 
		this.name = name; 
		this.salary = salary; 
	}
	public void display() { 
		System.out.println("---------------");
		System.out.println("Employee ID : " + id); 
		System.out.println("Employee Name : " + name); 
		System.out.println("Employee Salary: ₹" + salary); 
		System.out.println("---------------"); 
		}
	public void raiseSalary(double percent) { 
		if (percent > 0) { 
		double increment = salary * percent / 100; 
		salary += increment; 
		System.out.println("Salary increased by " + percent + "% (₹" + increment + ")"); 
		} else { 
		System.out.println("Invalid percentage. Salary not changed."); 
		}
	}
}


package program6;

public class Circle1 extends Shape1 {
	double r; 
	public Circle1(double radius) {
		r = radius;
	}
	void area() { 
		System.out.println("Area of Circle: " + (3.14 * r * r)); 
	} 
	void perimeter() { 
		System.out.println("Perimeter of Circle: " + (2 * 3.14 * r));
	}
}

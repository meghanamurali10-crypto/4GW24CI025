package program6;

public class Triangle1 extends Shape1 {
	double a,b,c;
	public Triangle1(double x, double y, double z) {
		a = x;
		b = y;
		c = z;
	}
	void area() 
	{ 
		double s = (a + b + c) / 2; 
		double ar = Math.sqrt(s * (s - a) * (s - b) * (s - c)); 
		System.out.println("Area of Triangle: " + ar); 
	}
	void perimeter() 
	{ 
		System.out.println("Perimeter of Triangle: " + (a + b + c)); 
	} 
}


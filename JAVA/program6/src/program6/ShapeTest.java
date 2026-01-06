package program6;

public class ShapeTest {

	public static void main(String[] args) {
		Circle1 c = new Circle1(5); 
		c.area(); 
		c.perimeter(); 
		System.out.println(); 
		Triangle1 t = new Triangle1(3, 4, 5); 
		t.area(); 
		t.perimeter(); 
	}

}

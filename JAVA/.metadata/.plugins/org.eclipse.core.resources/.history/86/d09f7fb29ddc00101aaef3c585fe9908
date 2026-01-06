package program4;

public class TestMyPoint {

	public static void main(String[] args) {
		MyPoint p1 = new MyPoint(); 
		MyPoint p2 = new MyPoint(3,4); 
		
		System.out.println("p1: " + p1);  
		System.out.println("p2: " + p2); 
		
		p1.setXY(5,6); 
		System.out.println("After setXY, p1: " + p1); 

		int[] coords = p1.getXY(); 
		System.out.println("p1 x: " + coords[0] + ", y: " + coords[1]); 

		double d1 = p1.distance(4, 6); 
		System.out.println("Distance from p1 to (4,6): " + d1); 

		double d2 = p1.distance(p2); 
		System.out.println("Distance from p1 to p2: " + d2); 

		double d3 = p2.distance(); 
		System.out.println("Distance from p2 to origin: " + d3);

	}

}

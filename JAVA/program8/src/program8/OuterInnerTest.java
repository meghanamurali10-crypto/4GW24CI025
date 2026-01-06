package program8;

public class OuterInnerTest {
	public static void main(String[] args) {
		Outer outer = new Outer();
		outer.display();
		
		Outer.Inner inner = outer.new Inner();
		inner.display();
		// TODO Auto-generated method stub

	}

}

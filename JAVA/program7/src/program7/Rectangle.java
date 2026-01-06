package program7;

public class Rectangle implements Resizable {
	int width;
	int height;
	public Rectangle(int w, int h) 
	{
		width = w;
		height = h;
	}
	void display() {
		System.out.println("Width: " + width + "  Height: " + height);
	}
	public void resizeWidth(int newWidth) {
		width = newWidth;
	}
	public void resizeHeight(int newHeight) {
		height = newHeight;
	}
}

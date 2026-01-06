package program2;
public class Stack {
	private int maxSize = 10;
	private int[] stackArray;
	private int top;
	public Stack() {
		stackArray = new int[maxSize];
		top = -1;
    // TODO Auto-generated constructor stub
	}
	public void push(int value) {
		if(top == maxSize-1) {
			System.out.println("Stack Overflow!! Cannot push" + value);
		}
		else {
			stackArray[++top]= value;
			System.out.println(value + "Pushed to stack");
		}
	}
	public int pop() {
		if (top== -1) {
			System.out.println("Stack underflow!! Stack is empty");
			return -1;
		}
		else {
			int value = stackArray[top--];
			System.out.println(value + "popped from stack");
			return value;
		}
	}
	public void display() {
		if (top == -1) {
			System.out.println("Stack is empty");
		}
		else {
			System.out.println("Stack elements are (Top to Bottom): ");
			for(int i=top;i>=0;i--) {
				System.out.println(stackArray[i]);
			 
			}
		}
	}

	//public static void main(String[] args) {
		// TODO Auto-generated method stub

	}


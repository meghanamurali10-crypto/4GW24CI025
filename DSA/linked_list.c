
#include <stdio.h>
#include <stdlib.h>

// Define the structure for a node
struct Node {
    int data;
    struct Node* next;
};

// Function to create a new node
struct Node* createNode(int data) {
    struct Node* newNode = (struct Node*)malloc(sizeof(struct Node));
    newNode->data = data;
    newNode->next = NULL;
    return newNode;
}

// Insert at end for testing
void insertEnd(struct Node** head, int data) {
    struct Node* newNode = createNode(data);
    if (*head == NULL) {
        *head = newNode;
        return;
    }
    struct Node* temp = *head;
    while (temp->next != NULL)
        temp = temp->next;
    temp->next = newNode;
}

// Delete from front
void deleteFront(struct Node** head) {
    if (*head == NULL) {
        printf("List is empty.\n");
        return;
    }
    struct Node* temp = *head;
    *head = (*head)->next;
    printf("Deleted from front: %d\n", temp->data);
    free(temp);
}

// Delete from rear
void deleteRear(struct Node** head) {
    if (*head == NULL) {
        printf("List is empty.\n");
        return;
    }
    if ((*head)->next == NULL) {
        printf("Deleted from rear: %d\n", (*head)->data);
        free(*head);
        *head = NULL;
        return;
    }
    struct Node* temp = *head;
    while (temp->next->next != NULL)
        temp = temp->next;
    printf("Deleted from rear: %d\n", temp->next->data);
    free(temp->next);
    temp->next = NULL;
}

// Delete from ith position (1-based)
void deleteAtPosition(struct Node** head, int pos) {
    if (*head == NULL || pos <= 0) {
        printf("Invalid position or empty list.\n");
        return;
    }
    if (pos == 1) {
        deleteFront(head);
        return;
    }
    struct Node* temp = *head;
    for (int i = 1; i < pos - 1 && temp->next != NULL; i++)
        temp = temp->next;

    if (temp->next == NULL) {
        printf("Position out of range.\n");
        return;
    }
    struct Node* toDelete = temp->next;
    temp->next = toDelete->next;
    printf("Deleted from position %d: %d\n", pos, toDelete->data);
    free(toDelete);
}

// Display the list
void display(struct Node* head) {
    printf("List: ");
    while (head != NULL) {
        printf("%d -> ", head->data);
        head = head->next;
    }
    printf("NULL\n");
}

// Main function to test
int main() {
    struct Node* head = NULL;

    // Sample insertions
    insertEnd(&head, 10);
    insertEnd(&head, 20);
    insertEnd(&head, 30);
    insertEnd(&head, 40);
    insertEnd(&head, 50);

    display(head);

    deleteFront(&head);
    display(head);

    deleteRear(&head);
    display(head);

    deleteAtPosition(&head, 2);
    display(head);

    return 0;
}

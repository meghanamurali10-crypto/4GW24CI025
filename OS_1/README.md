# Deadlock Study Program

##  Overview
This **C program** demonstrates **deadlock** conditions and **prevention strategies** using **POSIX threads (pthreads)** and **semaphores**. It creates two threads competing for two resources (`R1` and `R2`), showing both the **deadlock scenario** and how to **prevent it** using resource ordering.

Perfect for **Operating Systems coursework** on process synchronization and deadlock avoidance.

***

##  Features

- **Interactive Mode Selection:**  
  Choose between `DEADLOCK` demo (mode 1) or `SAFE PREVENTION` (mode 0).

- **Deadlock Demonstration:**  
  Exhibits all 4 deadlock conditions:
  - **Mutual Exclusion**
  - **Hold & Wait**
  - **No Preemption**
  - **Circular Wait**

- **Prevention Strategy:**  
  Implements **Resource Ordering** (ascending order acquisition) to break circular wait.

- **Visual Output:**  
  Clear console messages showing resource acquisition sequence and deadlock state.

***
**Team Members**

Meghana M - 4GW24CI025
Anjali Ajith - 4GW24CI003

***

##  Program Structure

```
deadlock_study/
├── deadlock_demo.c   # Main program file
```

### Key Components

| Component | Description |
|-----------|-------------|
| `main()` | Initializes semaphores, creates threads, and handles user input |
| `p1()` | Thread 1 - Acquires R1 → R2 (deadlock path) |
| `p2()` | Thread 2 - Acquires R2 → R1 (deadlock path) |
| `r1, r2` | Binary semaphores representing shared resources |

***

##  How to Run

### 1. **Compile**
```bash
gcc -o deadlock_demo deadlock_demo.c -lpthread
```

### 2. **Run Deadlock Demo**
```bash
./deadlock_demo
```
```
Enter 1 for DEADLOCK demo, 0 for SAFE prevention: 1
Mode: DEADLOCK (Ctrl+C to stop)

P1: Acquired R1 (Mutual Exclusion)
P2: Acquired R2 (Mutual Exclusion)
P1: Hold&Wait - Trying R2 (Circular Wait)  ← STUCK!
P2: Hold&Wait - Trying R1 (Circular Wait)  ← STUCK!
```

** Result:** Both threads **deadlock** (use `Ctrl+C` to stop).

### 3. **Run Safe Prevention**
```bash
./deadlock_demo
```
```
Enter 1 for DEADLOCK demo, 0 for SAFE prevention: 0
Mode: SAFE PREVENTION

P1: Acquired R1 (Mutual Exclusion)
P2: Acquired R2 (Mutual Exclusion)
P2: Safe - Release R2, Acquire R1 then R2
P1: Safe - Acquired R2 (Resource Ordering)
Program completed successfully!
```

** Result:** Both threads complete **without deadlock**.

***

##  Deadlock Analysis

### Deadlock Conditions (Mode 1)
```
P1 holds R1, waits for R2  ←──┐
P2 holds R2, waits for R1  └──→ CIRCULAR WAIT!
```

### Prevention Strategy (Mode 0)
```
Resource Ordering Rule: Always acquire resources in ASCENDING order (R1 → R2)
P2 releases R2 before acquiring R1 → Breaks circular wait
```

***

##  Expected Outputs

| Mode | Output Summary | Status |
|-----|----------------|--------|
| **1 (Deadlock)** | Both threads acquire 1 resource, then hang forever |  **DEADLOCK** |
| **0 (Safe)** | Both threads complete critical sections successfully |  **COMPLETED** |

***

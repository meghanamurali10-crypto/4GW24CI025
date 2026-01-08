# Git Workshop - HTML/CSS Project

A hands-on Git workshop project demonstrating version control basics through building a responsive personal website about independent women.

##  Workshop Overview

This project is designed for learning Git fundamentals while creating a real-world HTML/CSS website. Participants will practice essential Git commands including init, add, commit, push, pull, branching, and merging.

##  Learning Objectives

- Understand basic Git workflow
- Practice creating and managing repositories
- Learn branching and merging strategies
- Collaborate using GitHub
- Build a responsive HTML/CSS website
- Implement version control best practices

##  Project Description

A responsive personal website celebrating independence, strength, and authenticity in modern women. The site emphasizes the importance of finding one's sense of self while balancing various qualities.

### Core Message
> "We need women who are so strong they can be gentle, so educated they can be humble, so fierce they can be compassionate, so passionate they can be rational, and so disciplined they can be free."

##  Technologies Used

- **HTML5** - Structure and content
- **CSS3** - Styling and layout
- **Git** - Version control
- **GitHub** - Remote repository hosting

##  Project Structure

```
Git_workshop-project/
│
├── index.html          # Main HTML file
├── styles.css          # Stylesheet
├── script.js           # JavaScript file
└── README.md           # Project documentation
```

##  Git Workshop Setup

### Prerequisites
- Git installed on your system
- GitHub account
- Text editor (VS Code, Sublime Text, etc.)
- Web browser

### Workshop Steps

#### Step 1: Initialize Git Repository
```bash
# Navigate to your project folder
cd Git_workshop-project

# Initialize Git
git init

# Check status
git status
```

#### Step 2: Create Project Files
```bash
# Create HTML file
touch index.html

# Create CSS file
touch styles.css

# Create JavaScript file
touch script.js

# Create README
touch README.md
```

#### Step 3: Stage and Commit Changes
```bash
# Add files to staging area
git add .

# Or add specific files
git add index.html styles.css script.js

# Commit changes
git commit -m "Initial commit: Add HTML, CSS and JS files"
```

#### Step 4: Connect to GitHub
```bash
# Add remote repository
git remote add origin https://github.com/meghanamurali10-crypto/4GW24CI025.git

# Verify remote
git remote -v

# Push to GitHub
git push -u origin main
```

#### Step 5: Working with Branches
```bash
# Create a new branch
git checkout -b feature/add-content

# Make changes to files
# Stage and commit
git add .
git commit -m "Add inspirational content"

# Switch back to main
git checkout main

# Merge changes
git merge feature/add-content
```

##  Installation & Usage

1. **Clone the repository:**
```bash
git clone https://github.com/meghanamurali10-crypto/4GW24CI025.git
```

2. **Navigate to the project directory:**
```bash
cd 4GW24CI025/SkillLab/Git_workshop-project
```

3. **Open in browser:**
```bash
# On macOS
open index.html

# On Linux
xdg-open index.html

# On Windows
start index.html
```

##  Workshop Exercises

### Exercise 1: Basic Git Commands
- Initialize a Git repository
- Create and modify files
- Stage and commit changes
- View commit history

### Exercise 2: Branching
- Create a new branch for styling
- Make CSS changes
- Merge branch back to main
- Resolve any conflicts

### Exercise 3: Collaboration
- Fork the repository
- Clone your fork
- Make changes
- Create a pull request

### Exercise 4: Version Control
- View project history with `git log`
- Revert changes if needed
- Use `.gitignore` for unnecessary files

##  HTML/CSS Features Implemented

- **Responsive Design** - Mobile-friendly layout
- **Semantic HTML** - Proper structure and accessibility
- **CSS Flexbox/Grid** - Modern layout techniques
- **Typography** - Readable and attractive fonts
- **Color Scheme** - Cohesive visual design

##  Common Git Commands Reference

```bash
# Check status
git status

# View commit history
git log
git log --oneline

# Create branch
git branch branch-name

# Switch branch
git checkout branch-name

# Create and switch
git checkout -b branch-name

# Pull latest changes
git pull origin main

# Push changes
git push origin branch-name

# View differences
git diff
```

##  Workshop Goals Achieved

-  Created HTML structure
-  Styled with CSS
-  Added JavaScript functionality
-  Initialized Git repository
-  Made multiple commits
-  Created and merged branches
-  Pushed to GitHub
-  Documented with README


## Team Members

**Meghana M**
- GitHub: [@meghanamurali10-crypto](https://github.com/meghanamurali10-crypto)
**Anjali Ajith**
- GitHub: [@anjaliajithofficial-dev](https://github.com/anjaliajithofficial-dev)

##  License

This project is open source and available under the [MIT License](LICENSE).

---


*This project was created as part of a Git workshop to teach version control fundamentals through practical web development.*

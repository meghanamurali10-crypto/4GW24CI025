#  Actionable Health Assistant (AHA)

**Actionable Health Assistant (AHA)** is a lightweight, multilingual, web-based health guidance tool designed to provide **instant, actionable advice** based on user-reported symptoms.  
Built for **low-resource settings**, AHA bridges the gap between *basic health awareness* and *timely clinical intervention*.

This project was developed as part of the **GHCI Hackathon**.

---

##  Problem Statement

Many people delay seeking medical help due to:
- Lack of reliable, easy-to-understand guidance
- Language barriers
- Uncertainty about symptom severity
- Limited access to healthcare facilities

AHA addresses this by offering **symptom-smart guidance**, **risk alerts**, and **clinic routing** using a simple, accessible interface.

---

##  Solution Overview

AHA allows users to:
- Enter a **symptom or health keyword**
- Select their **preferred language**
- Receive:
  - Immediate **actionable advice**
  - Clear **risk alerts**
  - A **clinic link** when escalation is required
  - Emergency **helpline information**

The system uses a **serverless backend powered by Google Apps Script** and a **Google Sheet as a structured medical knowledge base**.

---

##  Architecture

```

User (Browser)
|
|  POST Request (keyword, language)
↓
HTML + CSS + JavaScript (Frontend)
|
↓
Google Apps Script (Serverless API)
|
↓
Google Sheet (Symptom → Action → Risk → Clinic)

````

---

##  Frontend Features

- Clean, responsive UI (HTML + CSS)
- Mobile-friendly design
- Language selection support (English, Hindi)
- Loading states and error handling
- Emergency helpline display
- Keyboard support (Enter key)

---

##  Backend Features (Google Apps Script)

- Acts as a REST-like API using `doPost()`
- Reads structured medical data from Google Sheets
- Language-aware column mapping
- Exact + partial keyword matching
- Query logging for analytics and impact tracking
- Fully serverless (no external backend required)

---

##  Data Structure (Google Sheet)

### Required Sheets
- **`dataSheet`** – stores symptom and guidance data
- **`queryLog`** – logs user searches

### Sample Columns
- `Keyword/Symptom`
- `Action 1: What to do now (English)`
- `Risk Alert: When to seek help (English)`
- `Action 1: What to do now (Hindi)`
- `Risk Alert: When to seek help (Hindi)`
- `Nearby Clinic Code/URL`

>  Column names must match **exactly** as used in the backend code.

---

##  Setup Instructions

###  Frontend
- Open the HTML file
- Update the variable:
```js
const appsScriptUrl = 'YOUR_DEPLOYED_WEB_APP_URL_HERE';
````

###  Backend (Google Apps Script)

1. Create a Google Sheet with the required structure
2. Open **Extensions → Apps Script**
3. Paste the backend code
4. Deploy as **Web App**

   * Execute as: *Me*
   * Access: *Anyone*
5. Copy the deployed URL and paste it into the frontend

---

##  Supported Languages

* English
* Hindi (हिंदी)

> Additional languages can be added by extending the sheet headers.

---

##  Emergency Handling

* Displays **India-standard emergency numbers**:

  * `112` / `108`
* Language-aware emergency instructions
* Clear **risk escalation alerts**

---

##  Impact & Use Cases

* First-level health guidance in rural or low-connectivity areas
* Reduces unnecessary panic and hospital visits
* Encourages early medical intervention when required
* Useful for NGOs, public health kiosks, and community clinics

---

##  Privacy & Ethics

* No personal user data collected
* No diagnosis claims
* Designed strictly as **guidance**, not medical diagnosis

---

##  Tech Stack

* **Frontend:** HTML, CSS, JavaScript
* **Backend:** Google Apps Script
* **Database:** Google Sheets
* **Deployment:** Serverless (Google Cloud)

---

##  Hackathon Note

This project emphasizes:

* Accessibility
* Scalability
* Low-cost deployment
* Real-world healthcare impact

---

##  Future Enhancements

* Fuzzy matching / NLP-based symptom detection
* Voice input for low-literacy users
* Offline-first PWA support
* Location-based clinic discovery
* More regional languages

---

##  Team

- **Meghana M**  
- **Anjali**

GHCI Hackathon Participant

---

##  License

This project is released under the **MIT License**.

```

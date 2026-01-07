/**
 * Actionable Health Assistant (AHA) - Google Apps Script Backend
 * This script runs entirely on Google's servers. It acts as a serverless
 * API endpoint, receiving POST requests from the client HTML page,
 * reading data from the Google Sheet, performing a lookup, and logging queries.
 * * This script uses the data structure defined in previous responses (e.g., 'Action 1: What to do now (English)').
 */

// --- Configuration Constants ---
const DATA_SHEET_NAME = 'dataSheet';
const LOG_SHEET_NAME = 'queryLog';


// --- Entry Point Function: Handles Incoming POST Requests ---

/**
 * The core function that receives the POST request from the HTML client (index.html).
 * It extracts the keyword, performs the lookup, and returns the structured advice.
 * * @param {Object} e The event object containing all the request parameters (keyword, lang).
 * @returns {GoogleAppsScript.Content.TextOutput} JSON response back to the client.
 */
function doPost(e) {
    // Initialize the response structure
    const response = {
        matchFound: false,
        closestMatch: null,
        action: 'No advice found.',
        riskAlert: 'No specific risk alert found.',
        clinicLink: null,
        lang: e.parameter.lang || 'English'
    };

    try {
        const keyword = e.parameter.keyword ? e.parameter.keyword.toLowerCase().trim() : '';
        const lang = e.parameter.lang || 'English';

        if (!keyword) {
            response.riskAlert = 'Error: No search term provided.';
            return createJsonResponse(response);
        }

        const ss = SpreadsheetApp.getActiveSpreadsheet();
        const sheet = ss.getSheetByName(DATA_SHEET_NAME);
        if (!sheet) {
            response.riskAlert = `Configuration Error: Data sheet '${DATA_SHEET_NAME}' not found. Check your sheet tab name.`;
            logQuery(ss, keyword, 'Sheet Error', lang);
            return createJsonResponse(response);
        }

        // Read all data from the sheet (fast bulk read)
        const data = sheet.getDataRange().getValues();
        const headers = data[0];
        const dataRows = data.slice(1);

        // Dynamic Header Mapping based on the sheet structure
        const keywordIndex = headers.indexOf('Keyword/Symptom');
        // We construct the localized header keys based on the language selected by the client
        const actionIndex = headers.indexOf(`Action 1: What to do now (${lang})`);
        const riskAlertIndex = headers.indexOf(`Risk Alert: When to seek help (${lang})`);
        const clinicIndex = headers.indexOf('Nearby Clinic Code/URL');

        // Check if critical columns for the requested language exist
        if (actionIndex === -1 || riskAlertIndex === -1) {
            response.riskAlert = `Language Error: Missing data columns for language: ${lang}. Ensure headers match exactly (e.g., 'Action 1: What to do now (${lang})').`;
            logQuery(ss, keyword, 'Lang Col Error', lang);
            return createJsonResponse(response);
        }

        // --- Core Logic: Simple Exact/Substring Match Lookup ---
        let bestMatchRow = null;
        let closestMatchKeyword = null;

        // Find the best match
        for (const row of dataRows) {
            const dbKeyword = row[keywordIndex] ? String(row[keywordIndex]).toLowerCase() : '';

            // 1. Check for Exact Match (Highest Priority)
            if (dbKeyword === keyword) {
                bestMatchRow = row;
                closestMatchKeyword = row[keywordIndex];
                break; // Stop immediately on perfect match
            }

            // 2. Simple Substring/Contains Match (Fallback for logging/closest match text)
            if (dbKeyword.includes(keyword) || keyword.includes(dbKeyword)) {
                if (!bestMatchRow) {
                    bestMatchRow = row; // Take the first relevant match as the result
                    closestMatchKeyword = row[keywordIndex];
                }
            }

            // Keep track of a checked keyword for "Closest Match" feedback
            if (!closestMatchKeyword && row[keywordIndex]) {
                closestMatchKeyword = row[keywordIndex];
            }
        }

        response.closestMatch = closestMatchKeyword;

        // Populate the final response object if a match was confirmed
        if (bestMatchRow) {
            response.matchFound = true;
            response.action = bestMatchRow[actionIndex] || 'Action text missing.';
            response.riskAlert = bestMatchRow[riskAlertIndex] || 'Risk alert text missing.';
            response.clinicLink = bestMatchRow[clinicIndex] || null;

            // Use the actual keyword from the sheet for display
            response.matchFound = bestMatchRow[keywordIndex];

            logQuery(ss, keyword, bestMatchRow[keywordIndex], lang);
        } else {
            logQuery(ss, keyword, 'No Match Found', lang);
        }

        return createJsonResponse(response);

    } catch (error) {
        Logger.log('Error in doPost: ' + error.toString());
        response.riskAlert = `A critical system error occurred on the server: ${error.message}.`;
        return createJsonResponse(response);
    }
}

// --- Utility Functions ---

/**
 * Utility function to create and return the JSON response to the client.
 */
function createJsonResponse(data) {
    // MimeType.JSON is essential for the client-side JavaScript's response.json() method to work
    return ContentService.createTextOutput(JSON.stringify(data))
        .setMimeType(ContentService.MimeType.JSON);
}

/**
 * Utility function to log the user's query for impact metrics and auditing.
 */
function logQuery(ss, inputKeyword, matchResult, lang) {
    try {
        const logSheet = ss.getSheetByName(LOG_SHEET_NAME) || ss.insertSheet(LOG_SHEET_NAME);

        // Ensure log sheet has headers if it's the first time running
        if (logSheet.getLastRow() === 0) {
            logSheet.appendRow(['Timestamp', 'User Input', 'Match Found', 'Language', 'User ID (N/A)']);
        }

        // Log the data in the QueryLog sheet
        logSheet.appendRow([
            new Date(),
            inputKeyword,
            matchResult,
            lang,
            'N/A'
        ]);
    } catch (e) {
        Logger.log('FATAL: Failed to log query data to sheet: ' + e.toString());
    }
}
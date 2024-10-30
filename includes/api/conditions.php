<?php

// VERSION 1.0.0

$logic = array();

$logic['default'] = <<<DEFAULT
"first_visit": {
	"label": "First Visit",
	"category": "Visitor Behavior",
	"map": "{\"#operator\": [ {\"var\":\"FirstVisit\"}, true ] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"FirstVisit\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "This is the user's first visit",
				"!=": "Not the user's first visit"
			}
		}
	],
	"info": "Is this the first time the user has visited the site."
},
"direct_visit": {
	"label": "Direct Visit",
	"category": "Visitor Behavior",
	"map": "{\"#operator\": [ {\"var\":\"Source\"}, \"direct\" ] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"Source\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "The user visited the site directly",
				"!=": "The user was referred by another site or link"
			}
		}
	],
	"info": "Has the user visited the site directly or were they referred from another site."
},
"total_visits": {
	"label": "Total Visits",
	"category": "Visitor Behavior",
	"map": "{\"#operator\": [ {\"var\":\"TotalVisits\"}, \"#value\" ] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"TotalVisits\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "number",
			"placeholder": "Value",
			"map": 1
		}
	],
	"info": "The visitor's total visits to the site."
},
"lead_score": {
	"label": "Lead Score",
	"category": "Visitor Behavior",
	"map": "{\"#operator\": [ {\"var\":\"LeadScore\"}, \"#value\" ] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"LeadScore\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "number",
			"placeholder": "Value",
			"map": 1
		}
	],
	"info": "The visitor's lead score."
},
"geo": {
	"label": "Location",
	"category": "Geolocation",
	"map": "{\"#operator\": [ {\"var\":\"Location.#var\"}, \"#value\" ] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"Location\\\.",
	"type": "valueSubStrIndex",
	"inputs": [
		{
			"label": "Location Type",
			"name": "var",
			"type": "select",
			"options": {
				"CountryCode": "Country Code (US, CA, etc)",
				"RegionCode": "State/Region Code (CA, NY, etc)",
				"City": "City",
				"ZIPCode": "ZIP Code",
				"MetroCode": "Metro Code"
			},
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Is",
				"!=": "Is Not",
				"in": "Is In List",
				"not_in": "Not In List",
				"eq_i": "Is - Case-Insensitive",
				"ne_i": "Is Not - Case-Insensitive",
				"in_i": "Is In List - Case-Insensitive",
				"not_in_i": "Not In List - Case-Insensitive"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 1
		}
	],
	"info": "Is the visitor located in a specific country, region, city, etc. Location is derived from IP address and may be imprecise."
},
"language": {
	"label": "Language",
	"category": "Visitor Metadata",
	"map": "{\"#operator\": [{\"var\":\"Language\"}, \"#value\" ] }",
	"lookup": "\\\[{\\\\\"var\\\\\":\\\\\"Language\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Is",
				"!=": "Is Not",
				"in": "Is In List",
				"not_in": "Not In List",
				"eq_i": "Is - Case-Insensitive",
				"ne_i": "Is Not - Case-Insensitive",
				"in_i": "Is In List - Case-Insensitive",
				"not_in_i": "Not In List - Case-Insensitive"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 1
		}
	],
	"info": "Is the visitor's browser set to a specific language."
},
"IP": {
	"label": "IP Address",
	"category": "Visitor Metadata",
	"map": "{\"#operator\": [{\"var\":\"IP\"}, \"#value\" ] }",
	"lookup": "\\\[{\\\\\"var\\\\\":\\\\\"IP\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Is",
				"!=": "Is Not",
				"in": "Is In List",
				"not_in": "Not In List"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 1
		}
	],
	"info": "Is the visitor coming from a specific IP Address."
},
"IP_Contains": {
	"label": "IP Address Contains",
	"category": "Visitor Metadata",
	"map": "{\"#operator\": [\"#value\",{\"var\":\"IP\"}]}",
	"lookup": "\\\\\",{\\\\\"var\\\\\":\\\\\"IP\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"in": "Contains",
				"not_in": "Does Not Contain",
				"in_i": "Contains - Case-Insensitive",
				"not_in_i": "Does Not Contain - Case-Insensitive"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 0
		}
	],
	"info": "Is the visitor coming from an IP Address that contains a value."
},
"operating_system": {
	"label": "Operating System",
	"category": "Visitor Device",
	"map": "{\"#operator\": [ {\"var\":\"OS\"}, \"#value\" ] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"OS\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Is",
				"!=": "Is Not"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "select",
			"options": {
				"Windows": "Windows",
				"macOS": "macOS",
				"Linux": "Linux",
				"iOS": "iOS",
				"Android": "Android",
				"BlackBerry": "BlackBerry",
				"Unknown": "Unknown"
			},
			"map": 1
		}
	],
	"info": "Which operating system is the visitor using."
},
"mobile": {
	"label": "Mobile Device",
	"category": "Visitor Device",
	"map": "{\"#operator\": [ {\"var\":\"Mobile\"}, true ] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"Mobile\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "True",
				"!=": "False"
			}
		}
	],
	"info": "Is the visitor on a mobile device. <em>(Smartphone or tablet)</em>"
},
"tablet": {
	"label": "Tablet",
	"category": "Visitor Device",
	"map": "{\"#operator\": [ {\"var\":\"Tablet\"}, true ] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"Tablet\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "True",
				"!=": "False"
			}
		}
	],
	"info": "Is the visitor using a tablet."
},
"mobileos": {
	"label": "Mobile OS",
	"category": "Visitor Device",
	"map": "{\"#operator\": [ {\"var\":\"MobileOS\"}, \"#value\" ] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"MobileOS\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Is",
				"!=": "Is Not"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 1
		}
	],
	"info": "Which mobile operating system: Android, iOS or empty"
},
"time_elapsed": {
	"label": "Time Elapsed",
	"category": "Visitor Behavior",
	"map": "{\"#operator\": [{\"-\":[{\"var\": \"Date.Timestamp\"},{\"var\":\"Timestamp.#time\"}]}, #elapsed]}",
	"lookup": "{\\\\\"var\\\\\":\\\\\"Timestamp.",
	"type": "default",
	"inputs": [
		{
			"label": "Time Since",
			"name": "time",
			"type": "select",
			"options": {
				"LastPage": "Since Last Page Viewed",
				"ThisVisit": "Since This Visit Started",
				"LastVisit": "Since Last Visit",
				"FirstVisit": "Since First Visit"
			},
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				">": "Greater Than",
				"<": "Less Than"
			}
		},
		{
			"label": "Time Elapsed",
			"name": "elapsed",
			"type": "select",
			"options": {
				"15": "Fifteen Seconds",
				"30": "Thirty Seconds",
				"60": "One Minute",
				"300": "Five Minutes",
				"600": "Ten Minutes",
				"900": "Fifteen Minutes",
				"1800": "Thirty Minutes",
				"3600": "One Hour",
				"7200": "Two Hours",
				"10800": "Three Hours",
				"14400": "Four Hours",
				"18000": "Five Hours",
				"21600": "Six Hours",
				"43200": "Twelve Hours",
				"86400": "One Day",
				"172800": "Two Days",
				"259200": "Three Days",
				"345600": "Four Days",
				"432000": "Five Days",
				"518400": "Six Days",
				"604800": "One Week",
				"1209600": "Two Weeks",
				"1814400": "Three Weeks",
				"2419200": "One Month",
				"4838400": "Two Months",
				"7257600": "Three Months"
			},
			"map": 1
		}
	],
	"info": "The amount of time elapsed since the user's first visit, last visit, the current visit started or the last page was viewed."
},
"goal_state": {
	"label": "Goal - All Visits",
	"category": "Logic Hop Goals",
	"map": "{\"#operator\": [ {\"key_exists\": [#goal, {\"var\":\"Goals\"}] }, true] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"Goals\\\\\"}]",
	"type": "valueKeyExists",
	"inputs": [
		{
			"label": "Goal Name",
			"name": "goal",
			"type": "ajax",
			"source": ["logichop-goals"],
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Completed",
				"!=": "Not Completed"
			}
		}
	],
	"info": "Has a specific Goal been completed or not completed by the visitor."
},
"goal_state_s": {
	"label": "Goal - Current Session",
	"category": "Logic Hop Goals",
	"map": "{\"#operator\": [ {\"key_exists\": [#goal, {\"var\":\"GoalsSession\"}] }, true] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"GoalsSession\\\\\"}",
	"type": "valueKeyExists",
	"inputs": [
		{
			"label": "Goal Name",
			"name": "goal",
			"type": "ajax",
			"source": ["logichop-goals"],
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Completed",
				"!=": "Not Completed"
			}
		}
	],
	"info": "Has a specific Goal been completed or not completed by the visitor during the current session."
},
"goal_specific_views": {
	"label": "Goal Count - All Visits",
	"category": "Logic Hop Goals",
	"map": "{\"#operator\": [ {\"var\":\"Goals.#goal\" }, #views ] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"Goals\\\.",
	"type": "valueSubStrIndex",
	"inputs": [
		{
			"label": "Goal",
			"name": "goal",
			"type": "ajax",
			"source": ["logichop-goals"],
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Views",
			"name": "views",
			"type": "number",
			"map": 1
		}
	],
	"info": "The number of times the visitor has completed a specific Goal."
},
"goal_specific_views_s": {
	"label": "Goal Count - Current Session",
	"category": "Logic Hop Goals",
	"map": "{\"#operator\": [ {\"var\":\"GoalsSession.#goal\" }, #views ] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"GoalsSession\\\.",
	"type": "valueSubStrIndex",
	"inputs": [
		{
			"label": "Goal",
			"name": "goal",
			"type": "ajax",
			"source": ["logichop-goals"],
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Views",
			"name": "views",
			"type": "number",
			"map": 1
		}
	],
	"info": "The number of times the visitor has completed a specific Goal during the current session."
},
"page_current": {
	"label": "Current Page",
	"category": "Visitor Behavior",
	"map": "{\"#operator\": [ {\"var\":\"Page\"}, #page ] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"Page\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Is",
				"!=": "Is Not"
			}
		},
		{
			"label": "Page or Post Title",
			"name": "page",
			"type": "ajax",
			"source": ["page", "post"],
			"map": 1
		}
	],
	"info": "Is the current page a specific page or post."
},
"page_current_views": {
	"label": "Current Page Views - All Visits",
	"category": "Visitor Behavior",
	"map": "{\"#operator\": [ {\"var\":\"Views\"}, #views ] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"Views\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Views",
			"name": "views",
			"map": 1,
			"type": "number"
		}
	],
	"info": "The number of times the current page has been viewed by the visitor."
},
"page_current_views_s": {
	"label": "Current Page Views - Current Session",
	"category": "Visitor Behavior",
	"map": "{\"#operator\": [ {\"var\":\"ViewsSession\"}, #views ] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"ViewsSession\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Views",
			"name": "views",
			"map": 1,
			"type": "number"
		}
	],
	"info": "The number of times the current page has been viewed by the visitor during the current session."
},
"pages_total_views": {
	"label": "Total Page Views - All Visits",
	"category": "Visitor Behavior",
	"map": "{\"#operator\": [ {\"add_array\":{\"var\":\"Pages\"}}, #views ] }",
	"lookup": "add_array\\\\\":{\\\\\"var\\\\\":\\\\\"Pages\\\\\"",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Views",
			"name": "views",
			"map": 1,
			"type": "number"
		}
	],
	"info": "The number of all page views combined for the visitor."
},
"pages_total_views_s": {
	"label": "Total Page Views - Current Session",
	"category": "Visitor Behavior",
	"map": "{\"#operator\": [ {\"add_array\":{\"var\":\"PagesSession\"}}, #views ] }",
	"lookup": "add_array\\\\\":{\\\\\"var\\\\\":\\\\\"PagesSession",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Views",
			"name": "views",
			"map": 1,
			"type": "number"
		}
	],
	"info": "The number of all page views combined during the current session."
},
"page_specific_views": {
	"label": "Specific Page Views - All Visits",
	"category": "Visitor Behavior",
	"map": "{\"#operator\": [ {\"var\":\"Pages.#page\" }, #views ] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"Pages\\\.",
	"type": "valueSubStrIndex",
	"inputs": [
		{
			"label": "Page or Post Title",
			"name": "page",
			"type": "ajax",
			"source": ["page", "post"],
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Views",
			"name": "views",
			"type": "number",
			"map": 1
		}
	],
	"info": "The number of times the user has viewed a specific page."
},
"page_specific_views_s": {
	"label": "Specific Page Views - Current Session",
	"category": "Visitor Behavior",
	"map": "{\"#operator\": [ {\"var\":\"PagesSession.#page\" }, #views ] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"PagesSession\\\.",
	"type": "valueSubStrIndex",
	"inputs": [
		{
			"label": "Page or Post Title",
			"name": "page",
			"type": "ajax",
			"source": ["page", "post"],
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Views",
			"name": "views",
			"type": "number",
			"map": 1
		}
	],
	"info": "The number of times the user has viewed a specific page during the current session."
},
"url_path": {
	"label": "URL Path",
	"category": "Visitor Behavior",
	"map": "{\"#operator\": [\"#text\", {\"var\":\"URL\"}]}",
	"lookup": "{\\\\\"var\\\\\":\\\\\"URL\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Is",
				"!=": "Is Not",
				"in": "Contains",
				"not_in": "Does Not Contain",
				"eq_i": "Is - Case-Insensitive",
				"ne_i": "Is Not - Case-Insensitive",
				"in_i": "Contains - Case-Insensitive",
				"not_in_i": "Does Not Contain - Case-Insensitive"
			}
		},
		{
			"label": "Text",
			"name": "text",
			"type": "text",
			"map": 0
		}
	],
	"info": "The current URL path.<br>Everything following the domain name.<br>Does not include query strings."
},
"referrer": {
	"label": "Referrer",
	"category": "Visitor Behavior",
	"map": "{\"#operator\": [\"#url\", {\"var\":\"Referrer\"}]}",
	"lookup": "{\\\\\"var\\\\\":\\\\\"Referrer\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Is",
				"!=": "Is Not",
				"in": "Contains",
				"not_in": "Does Not Contain",
				"eq_i": "Is - Case-Insensitive",
				"ne_i": "Is Not - Case-Insensitive",
				"in_i": "Contains - Case-Insensitive",
				"not_in_i": "Does Not Contain - Case-Insensitive"
			}
		},
		{
			"label": "URL",
			"name": "url",
			"type": "text",
			"map": 0
		}
	],
	"info": "The current referring URL of the current visitor.<br>Full path including query string.<br>Internal and external referrers."
},
"landing_page": {
	"label": "Landing Page - First Visit",
	"category": "Visitor Behavior",
	"map": "{\"#operator\": [\"#slug\", {\"var\":\"LandingPage\"}]}",
	"lookup": "{\\\\\"var\\\\\":\\\\\"LandingPage\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Is",
				"!=": "Is Not",
				"in": "Contains",
				"not_in": "Does Not Contain",
				"eq_i": "Is - Case-Insensitive",
				"ne_i": "Is Not - Case-Insensitive",
				"in_i": "Contains - Case-Insensitive",
				"not_in_i": "Does Not Contain - Case-Insensitive"
			}
		},
		{
			"label": "Page Slug",
			"name": "slug",
			"type": "text",
			"map": 0
		}
	],
	"info": "The visitor's landing page from the current visit.<br>WordPress Slug with slashes.<br>Example: /about-page/<br> Home page is a single slash: /"
},
"landing_page_session": {
	"label": "Landing Page - Current Visit",
	"category": "Visitor Behavior",
	"map": "{\"#operator\": [\"#path\", {\"var\":\"LandingPageSession\"}]}",
	"lookup": "{\\\\\"var\\\\\":\\\\\"LandingPageSession\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Is",
				"!=": "Is Not",
				"in": "Contains",
				"not_in": "Does Not Contain",
				"eq_i": "Is - Case-Insensitive",
				"ne_i": "Is Not - Case-Insensitive",
				"in_i": "Contains - Case-Insensitive",
				"not_in_i": "Does Not Contain - Case-Insensitive"
			}
		},
		{
			"label": "Page Slug",
			"name": "path",
			"type": "text",
			"map": 0
		}
	],
	"info": "The visitor's landing page from the current visit.<br>WordPress Slug with slashes.<br>Example: /about-page/<br> Home page is a single slash: /"
},
"query": {
	"label": "Query String",
	"category": "URL Parameters",
	"map": "{\"#operator\": [ {\"var\":\"Query.#var\" }, \"#value\" ] }",
	"lookup": "\\\[{\\\\\"var\\\\\":\\\\\"Query\\\.",
	"type": "valueSubStrIndex",
	"inputs": [
		{
			"label": "Variable",
			"name": "var",
			"type": "text",
			"placeholder": "Variable",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Is",
				"!=": "Is Not",
				"in": "Is In List",
				"not_in": "Not In List",
				"eq_i": "Is - Case-Insensitive",
				"ne_i": "Is Not - Case-Insensitive",
				"in_i": "Is In List - Case-Insensitive",
				"not_in_i": "Not In List - Case-Insensitive"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 1
		}
	],
	"info": "Is the query string variable set to a specific value.<br>Example: http://logichop.com/?animal=kangaroo.<br>Variable is \\\\\"animal\\\\\", value is \\\\\"kangaroo\\\\\""
},
"query_stored": {
	"label": "Query String Session",
	"category": "URL Parameters",
	"map": "{\"#operator\": [ {\"var\":\"QueryStore.#var\" }, \"#value\" ] }",
	"lookup": "\\\[{\\\\\"var\\\\\":\\\\\"QueryStore\\\.",
	"type": "valueSubStrIndex",
	"inputs": [
		{
			"label": "Variable",
			"name": "var",
			"type": "text",
			"placeholder": "Variable",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Is",
				"!=": "Is Not",
				"in": "Is In List",
				"not_in": "Not In List",
				"eq_i": "Is - Case-Insensitive",
				"ne_i": "Is Not - Case-Insensitive",
				"in_i": "Is In List - Case-Insensitive",
				"not_in_i": "Not In List - Case-Insensitive"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 1
		}
	],
	"info": "Has the query string variable with the specific value been set during this session.<br>Example: http://logichop.com/?animal=kangaroo.<br>Variable is \\\\\"animal\\\\\", value is \\\\\"kangaroo\\\\\""
},
"query_contains": {
	"label": "Query String Contains",
	"category": "URL Parameters",
	"map": "{\"#operator\": [ \"#value\",{ \"var\":\"Query.#var\" } ] }",
	"lookup": ",{\\\\\"var\\\\\":\\\\\"Query\\\.",
	"type": "valueSubStrIndex",
	"inputs": [
		{
			"label": "Variable",
			"name": "var",
			"type": "text",
			"placeholder": "Variable",
			"map": 1
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"in": "Contains",
				"not_in": "Does Not Contain",
				"in_i": "Contains - Case-Insensitive",
				"not_in_i": "Does Not Contain - Case-Insensitive"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 0
		}
	],
	"info": "Does the query string variable contains a value.<br>Example: http://logichop.com/?animal=kangaroo.<br>Variable is \\\\\"animal\\\\\", value is \\\\\"roo\\\\\""
},
"query_stored_contains": {
	"label": "Query String Session Contains",
	"category": "URL Parameters",
	"map": "{\"#operator\": [ \"#value\",{ \"var\":\"QueryStore.#var\" } ] }",
	"lookup": ",{\\\\\"var\\\\\":\\\\\"QueryStore\\\.",
	"type": "valueSubStrIndex",
	"inputs": [
		{
			"label": "Variable",
			"name": "var",
			"type": "text",
			"placeholder": "Variable",
			"map": 1
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"in": "Contains",
				"not_in": "Does Not Contain",
				"in_i": "Contains - Case-Insensitive",
				"not_in_i": "Does Not Contain - Case-Insensitive"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 0
		}
	],
	"info": "Has the query string variable that contains the value been set during this session.<br>Example: http://logichop.com/?animal=kangaroo.<br>Variable is \\\\\"animal\\\\\", value is \\\\\"roo\\\\\""
},
"query": {
	"label": "UTM Parameter",
	"category": "URL Parameters",
	"map": "{\"#operator\": [ {\"var\":\"Query.#var\" }, \"#value\" ] }",
	"lookup": "\\\[{\\\\\"var\\\\\":\\\\\"Query\\\.",
	"type": "valueSubStrIndex",
	"inputs": [
		{
			"label": "Variable",
			"name": "var",
			"type": "select",
			"options": {
				"utm_source": "utm_source",
				"utm_medium": "utm_medium",
				"utm_campaign": "utm_campaign",
				"utm_term": "utm_term",
				"utm_content": "utm_content"
			},
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Is",
				"!=": "Is Not",
				"in": "Is In List",
				"not_in": "Not In List",
				"eq_i": "Is - Case-Insensitive",
				"ne_i": "Is Not - Case-Insensitive",
				"in_i": "Is In List - Case-Insensitive",
				"not_in_i": "Not In List - Case-Insensitive"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 1
		}
	],
	"info": "Is a UTM parameter set to a specific value.<br>Example: http://logichop.com/?utm_source=plugin.<br>Variable is \\\\\"utm_source\\\\\", value is \\\\\"plugin\\\\\""
},
"in_category": {
	"label": "In Category",
	"category": "User Content Viewed",
	"map": "{\"#operator\": [ {\"key_exists\": [#category, {\"var\": \"Category\" }] }, true] }",
	"lookup": "Category",
	"type": "valueKeyExists",
	"inputs": [
		{
			"label": "Category",
			"name": "category",
			"type": "ajax",
			"source": ["category"],
			"query": "terms",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "In Category",
				"!=": "Not In Category"
			}
		}
	],
	"info": "Is the post in a category."
},
"category": {
	"label": "Category Views - All Visits",
	"category": "User Content Viewed",
	"map": "{\"#operator\": [ {\"var\": \"Categories.#category\" }, \"#views\" ] }",
	"lookup": "Categories\\\.",
	"type": "valueSubStrLastIndex",
	"inputs": [
		{
			"label": "Category",
			"name": "category",
			"type": "ajax",
			"source": ["category"],
			"query": "terms",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<=": "Less Than or Equal To",
				">=": "Greater Than or Equal To",
				"<": "Less Than",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Views",
			"name": "views",
			"type": "number",
			"map": 1
		}
	],
	"info": "Has the category been viewed during any visit."
},
"category_session": {
	"label": "Category Views - Current Session",
	"category": "User Content Viewed",
	"map": "{\"#operator\": [ {\"var\": \"CategoriesSession.#category\" }, \"#views\" ] }",
	"lookup": "CategoriesSession\\\.",
	"type": "valueSubStrLastIndex",
	"inputs": [
		{
			"label": "Category",
			"name": "category",
			"type": "ajax",
			"source": ["category"],
			"query": "terms",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<=": "Less Than or Equal To",
				">=": "Greater Than or Equal To",
				"<": "Less Than",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Views",
			"name": "views",
			"type": "number",
			"map": 1
		}
	],
	"info": "Has the category been viewed during the current session."
},
"has_tag": {
	"label": "Has Tag",
	"category": "User Content Viewed",
	"map": "{\"#operator\": [ {\"key_exists\": [\"#tag\", {\"var\": \"Tag\" }] }, true] }",
	"lookup": "Tag\"",
	"type": "valueKeyExists",
	"inputs": [
		{
			"label": "Tag",
			"name": "tag",
			"type": "ajax",
			"source": ["post_tag"],
			"query": "terms",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Has Tag",
				"!=": "Does Not Have Tag"
			}
		}
	],
	"info": "Does the post have a tag."
},
"tag": {
	"label": "Tag Views - All Visits",
	"category": "User Content Viewed",
	"map": "{\"#operator\": [ {\"var\": \"Tags.#tag\" }, \"#views\" ] }",
	"lookup": "Tags\\\.",
	"type": "valueSubStrLastIndex",
	"inputs": [
		{
			"label": "Tag",
			"name": "tag",
			"type": "ajax",
			"source": ["post_tag"],
			"query": "terms",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<=": "Less Than or Equal To",
				">=": "Greater Than or Equal To",
				"<": "Less Than",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Views",
			"name": "views",
			"type": "number",
			"map": 1
		}
	],
	"info": "Has the tag been viewed during any visit."
},
"tag_session": {
	"label": "Tag Views - Current Session",
	"category": "User Content Viewed",
	"map": "{\"#operator\": [ {\"var\": \"TagsSession.#tag\" }, \"#views\" ] }",
	"lookup": "TagsSession\\\.",
	"type": "valueSubStrLastIndex",
	"inputs": [
		{
			"label": "Tag",
			"name": "tag",
			"type": "ajax",
			"source": ["post_tag"],
			"query": "terms",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<=": "Less Than or Equal To",
				">=": "Greater Than or Equal To",
				"<": "Less Than",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Views",
			"name": "views",
			"type": "number",
			"map": 1
		}
	],
	"info": "Has the tag been viewed during the current session."
},
"loggedin": {
	"label": "User Is",
	"category": "Visitor Behavior",
	"map": "{\"#operator\": [ {\"var\":\"LoggedIn\"}, true ] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"LoggedIn\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Logged In",
				"!=": "Not Logged In"
			}
		}
	],
	"info": "Is the user currently logged in to WordPress"
},
"userdata": {
	"label": "User Data",
	"category": "Visitor Data",
	"map": "{\"#operator\": [ {\"var\":\"UserData.#var\"}, \"#value\" ] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"UserData\\\.",
	"type": "valueSubStrIndex",
	"inputs": [
		{
			"label": "User Data",
			"name": "var",
			"type": "select",
			"options": {
				"role": "Role",
				"user_email": "Email Address",
				"user_firstname": "First Name",
				"user_lastname": "Last Name",
				"display_name": "Display Name",
				"user_nicename": "Nice Name",
				"ID": "User ID"
			},
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Is",
				"!=": "Is Not",
				"in": "Is In List",
				"not_in": "Not In List",
				"eq_i": "Is - Case-Insensitive",
				"ne_i": "Is Not - Case-Insensitive",
				"in_i": "Is In List - Case-Insensitive",
				"not_in_i": "Not In List - Case-Insensitive"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 1
		}
	],
	"info": "Is the current user data set to a specific value."
},
"date_weekday": {
	"label": "Day of the Week",
	"category": "Time",
	"map": "{\"#operator\": [{\"var\":\"Date.DayNumber\"}, #day]}",
	"lookup": "{\\\\\"var\\\\\":\\\\\"Date.DayNumber\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Day",
			"name": "day",
			"type": "select",
			"options": {
				"1": "Monday",
				"2": "Tuesday",
				"3": "Wednesday",
				"4": "Thursday",
				"5": "Friday",
				"6": "Saturday",
				"7": "Sunday"
			},
			"map": 1
		}
	],
	"info": "The current day of the week starting with Monday, ending with Sunday.<br>Tuesday is less than Friday.<br>Saturday is greater than Wednesday.<br>Based on WordPress date & time."
},
"date_day": {
	"label": "Day",
	"category": "Time",
	"map": "{\"#operator\": [{\"var\":\"Date.Day\"}, #day]}",
	"lookup": "{\\\\\"var\\\\\":\\\\\"Date.Day\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Day",
			"name": "day",
			"type": "select",
			"options": {
				"1": 1,
				"2": 2,
				"3": 3,
				"4": 4,
				"5": 5,
				"6": 6,
				"7": 7,
				"8": 8,
				"9": 9,
				"10": 10,
				"11": 11,
				"12": 12,
				"13": 13,
				"14": 14,
				"15": 15,
				"16": 16,
				"17": 17,
				"18": 18,
				"19": 19,
				"20": 20,
				"21": 21,
				"22": 22,
				"23": 23,
				"24": 24,
				"25": 25,
				"26": 26,
				"27": 27,
				"28": 28,
				"29": 29,
				"30": 30,
				"31": 31
			},
			"map": 1
		}
	],
	"info": "The current numerical day of the month.<br>Based on WordPress date & time."
},
"date_month": {
	"label": "Month",
	"category": "Time",
	"map": "{\"#operator\": [{\"var\":\"Date.Month\"}, #month]}",
	"lookup": "{\\\\\"var\\\\\":\\\\\"Date.Month\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Month",
			"name": "month",
			"type": "select",
			"options": {
				"1": "January",
				"2": "February",
				"3": "March",
				"4": "April",
				"5": "May",
				"6": "June",
				"7": "July",
				"8": "August",
				"9": "September",
				"10": "October",
				"11": "November",
				"12": "December"
			},
			"map": 1
		}
	],
	"info": "The current month of the year starting with January, ending with December.<br>March is less than July.<br>October is greater than April.<br>Based on WordPress date & time."
},
"date_year": {
	"label": "Year",
	"category": "Time",
	"map": "{\"#operator\": [{\"var\":\"Date.Year\"}, #year]}",
	"lookup": "{\\\\\"var\\\\\":\\\\\"Date.Year\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Year",
			"name": "year",
			"type": "select",
			"options": {
				"2016": "2016",
				"2017": "2017",
				"2018": "2018",
				"2019": "2019",
				"2020": "2020"
			},
			"map": 1
		}
	],
	"info": "The current year.<br>Based on WordPress date & time."
},
"date_hour": {
	"label": "Hour",
	"category": "Time",
	"map": "{\"#operator\": [ {\"var\":\"Date.Hour24\"}, #hour ] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"Date.Hour24\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Hour",
			"name": "hour",
			"type": "select",
			"options": {
				"0": "12 am",
				"1": " 1 am",
				"2": " 2 am",
				"3": " 3 am",
				"4": " 4 am",
				"5": " 5 am",
				"6": " 6 am",
				"7": " 7 am",
				"8": " 8 am",
				"9": " 9 am",
				"10": "10 am",
				"11": "11 am",
				"12": "12 pm",
				"13": " 1 pm",
				"14": " 2 pm",
				"15": " 3 pm",
				"16": " 4 pm",
				"17": " 5 pm",
				"18": " 6 pm",
				"19": " 7 pm",
				"20": " 8 pm",
				"21": " 9 pm",
				"22": "10 pm",
				"23": "11 pm"
			},
			"map": 1
		}
	],
	"info": "The current hour of the day.<br>2am is less than 1pm.<br>11pm is greater than 12am.<br>Based on WordPress date & time."
},
"date_minutes": {
	"label": "Minutes",
	"category": "Time",
	"map": "{\"#operator\": [ {\"var\":\"Date.Minutes\"}, #minutes ] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"Date.Minutes\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Minutes",
			"name": "minutes",
			"type": "select",
			"options": {
				"0": ":00",
				"1": ":01",
				"2": ":02",
				"3": ":03",
				"4": ":04",
				"5": ":05",
				"6": ":06",
				"7": ":07",
				"8": ":08",
				"9": ":09",
				"10": ":10",
				"11": ":11",
				"12": ":12",
				"13": ":13",
				"14": ":14",
				"15": ":15",
				"16": ":16",
				"17": ":17",
				"18": ":18",
				"19": ":19",
				"20": ":20",
				"21": ":21",
				"22": ":22",
				"23": ":23",
				"24": ":24",
				"25": ":25",
				"26": ":26",
				"27": ":27",
				"28": ":28",
				"29": ":29",
				"30": ":30",
				"31": ":31",
				"32": ":32",
				"33": ":33",
				"34": ":34",
				"35": ":35",
				"36": ":36",
				"37": ":37",
				"38": ":38",
				"39": ":39",
				"40": ":40",
				"41": ":41",
				"42": ":42",
				"43": ":43",
				"44": ":44",
				"45": ":45",
				"46": ":46",
				"47": ":47",
				"48": ":48",
				"49": ":49",
				"50": ":50",
				"51": ":51",
				"52": ":52",
				"53": ":53",
				"54": ":54",
				"55": ":55",
				"56": ":56",
				"57": ":57",
				"58": ":58",
				"59": ":59"
			},
			"map": 1
		}
	],
	"info": "The current minute of the hour.<br>Most useful with greater than or less than.<br>Based on WordPress date & time."
},
"date_ymd": {
	"label": "Date",
	"category": "Time",
	"map": "{\"#operator\": [ {\"var\":\"Date.Date\"}, \"#date\"]}",
	"lookup": "{\\\\\"var\\\\\":\\\\\"Date.Date\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Date",
			"name": "date",
			"type": "text",
			"placeholder": "mm-dd-yyyy",
			"map": 1
		}
	],
	"info": "The current date.<br>Format: mm-dd-yyyy.<br>Halloween 2020 is 10-31-2020.<br>Based on WordPress date & time."
},
"user_date_weekday": {
	"label": "User's Day of the Week",
	"category": "Time",
	"map": "{\"#operator\": [{\"var\":\"UserDate.DayNumber\"}, #day]}",
	"lookup": "{\\\\\"var\\\\\":\\\\\"UserDate.DayNumber\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Day",
			"name": "day",
			"type": "select",
			"options": {
				"1": "Monday",
				"2": "Tuesday",
				"3": "Wednesday",
				"4": "Thursday",
				"5": "Friday",
				"6": "Saturday",
				"7": "Sundary"
			},
			"map": 1
		}
	],
	"info": "The user's current day of the week starting with Monday, ending with Sunday.<br>Tuesday is less than Friday.<br>Saturday is greater than Wednesday.<br>Based on the user's local date & time."
},
"user_date_day": {
	"label": "User's Day",
	"category": "Time",
	"map": "{\"#operator\": [{\"var\":\"UserDate.Day\"}, #day]}",
	"lookup": "{\\\\\"var\\\\\":\\\\\"UserDate.Day\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Day",
			"name": "day",
			"type": "select",
			"options": {
				"1": 1,
				"2": 2,
				"3": 3,
				"4": 4,
				"5": 5,
				"6": 6,
				"7": 7,
				"8": 8,
				"9": 9,
				"10": 10,
				"11": 11,
				"12": 12,
				"13": 13,
				"14": 14,
				"15": 15,
				"16": 16,
				"17": 17,
				"18": 18,
				"19": 19,
				"20": 20,
				"21": 21,
				"22": 22,
				"23": 23,
				"24": 24,
				"25": 25,
				"26": 26,
				"27": 27,
				"28": 28,
				"29": 29,
				"30": 30,
				"31": 31
			},
			"map": 1
		}
	],
	"info": "The user's current numerical day of the month.<br>Based on the user's local date & time."
},
"user_date_month": {
	"label": "User's Month",
	"category": "Time",
	"map": "{\"#operator\": [{\"var\":\"UserDate.Month\"}, #month]}",
	"lookup": "{\\\\\"var\\\\\":\\\\\"UserDate.Month\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Month",
			"name": "month",
			"type": "select",
			"options": {
				"1": "January",
				"2": "February",
				"3": "March",
				"4": "April",
				"5": "May",
				"6": "June",
				"7": "July",
				"8": "August",
				"9": "September",
				"10": "October",
				"11": "November",
				"12": "December"
			},
			"map": 1
		}
	],
	"info": "The user's current month of the year starting with January, ending with December.<br>March is less than July.<br>October is greater than April.<br>Based on the user's local date & time."
},
"user_date_year": {
	"label": "User's Year",
	"category": "Time",
	"map": "{\"#operator\": [{\"var\":\"UserDate.Year\"}, #year]}",
	"lookup": "{\\\\\"var\\\\\":\\\\\"UserDate.Year\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Year",
			"name": "year",
			"type": "select",
			"options": {
				"2016": "2016",
				"2017": "2017",
				"2018": "2018",
				"2019": "2019",
				"2020": "2020"
			},
			"map": 1
		}
	],
	"info": "The user's current year.<br>Based on the user's local date & time."
},
"user_date_hour": {
	"label": "User's Hour",
	"category": "Time",
	"map": "{\"#operator\": [ {\"var\":\"UserDate.Hour24\"}, #hour ] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"UserDate.Hour24\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Hour",
			"name": "hour",
			"type": "select",
			"options": {
				"0": "12 am",
				"1": " 1 am",
				"2": " 2 am",
				"3": " 3 am",
				"4": " 4 am",
				"5": " 5 am",
				"6": " 6 am",
				"7": " 7 am",
				"8": " 8 am",
				"9": " 9 am",
				"10": "10 am",
				"11": "11 am",
				"12": "12 pm",
				"13": " 1 pm",
				"14": " 2 pm",
				"15": " 3 pm",
				"16": " 4 pm",
				"17": " 5 pm",
				"18": " 6 pm",
				"19": " 7 pm",
				"20": " 8 pm",
				"21": " 9 pm",
				"22": "10 pm",
				"23": "11 pm"
			},
			"map": 1
		}
	],
	"info": "The user's current hour of the day.<br>2am is less than 1pm.<br>11pm is greater than 12am.<br>Based on the user's local date & time."
},
"user_date_minutes": {
	"label": "User's Minutes",
	"category": "Time",
	"map": "{\"#operator\": [ {\"var\":\"UserDate.Minutes\"}, #minutes ] }",
	"lookup": "{\\\\\"var\\\\\":\\\\\"UserDate.Minutes\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Minutes",
			"name": "minutes",
			"type": "select",
			"options": {
				"0": ":00",
				"1": ":01",
				"2": ":02",
				"3": ":03",
				"4": ":04",
				"5": ":05",
				"6": ":06",
				"7": ":07",
				"8": ":08",
				"9": ":09",
				"10": ":10",
				"11": ":11",
				"12": ":12",
				"13": ":13",
				"14": ":14",
				"15": ":15",
				"16": ":16",
				"17": ":17",
				"18": ":18",
				"19": ":19",
				"20": ":20",
				"21": ":21",
				"22": ":22",
				"23": ":23",
				"24": ":24",
				"25": ":25",
				"26": ":26",
				"27": ":27",
				"28": ":28",
				"29": ":29",
				"30": ":30",
				"31": ":31",
				"32": ":32",
				"33": ":33",
				"34": ":34",
				"35": ":35",
				"36": ":36",
				"37": ":37",
				"38": ":38",
				"39": ":39",
				"40": ":40",
				"41": ":41",
				"42": ":42",
				"43": ":43",
				"44": ":44",
				"45": ":45",
				"46": ":46",
				"47": ":47",
				"48": ":48",
				"49": ":49",
				"50": ":50",
				"51": ":51",
				"52": ":52",
				"53": ":53",
				"54": ":54",
				"55": ":55",
				"56": ":56",
				"57": ":57",
				"58": ":58",
				"59": ":59"
			},
			"map": 1
		}
	],
	"info": "The user's current minute of the hour.<br>Most useful with greater than or less than.<br>Based on the user's local date & time."
},
"user_date_ymd": {
	"label": "User's Date",
	"category": "Time",
	"map": "{\"#operator\": [ {\"var\":\"UserDate.Date\"}, \"#date\"]}",
	"lookup": "{\\\\\"var\\\\\":\\\\\"UserDate.Date\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Date",
			"name": "date",
			"type": "text",
			"placeholder": "mm-dd-yyyy",
			"map": 1
		}
	],
	"info": "The user's current date.<br>Format: mm-dd-yyyy.<br>Halloween 2020 is 10-31-2020.<br>Based on the user's local date & time."
},
"path": {
	"label": "User Journey",
	"category": "Visitor Behavior",
	"map": "{\"#operator\": [{\"compare_array_slice\": [[#pages], {\"var\":\"Path\"}]}, true]}",
	"lookup": "{\\\\\"var\\\\\":\\\\\"Path\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Is",
				"!=": "Is Not"
			}
		},
		{
			"label": "Page 1",
			"name": "page-1",
			"type": "ajax",
			"source": ["page", "post"],
			"map": 0
		},
		{
			"label": "Page 2",
			"name": "page-2",
			"type": "ajax",
			"source": ["page", "post"],
			"map": 1
		},
		{
			"label": "Page 3",
			"name": "page-3",
			"type": "ajax",
			"source": ["page", "post"],
			"map": 2
		},
		{
			"label": "Page 4",
			"name": "page-4",
			"type": "ajax",
			"source": ["page", "post"],
			"map": 3
		},
		{
			"label": "Page 5",
			"name": "page-5",
			"type": "ajax",
			"source": ["page", "post"],
			"map": 4
		}
	],
	"info": "The current visitor's path through the site. Up to 5 pages.<br>Leave pages unselected for fewer than 5.<br>User's path always consists of the 5 most recent pages."
},
"cookie": {
	"label": "Cookie",
	"category": "Visitor Metadata",
	"map": "{\"#operator\": [ {\"var\":\"Cookie.#var\" }, \"#value\" ] }",
	"lookup": "\\\[{\\\\\"var\\\\\":\\\\\"Cookie\\\.",
	"type": "valueSubStrIndex",
	"inputs": [
		{
			"label": "Cookie Name",
			"name": "var",
			"type": "text",
			"placeholder": "Cookie Name",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Is",
				"!=": "Is Not",
				"in": "Is In List",
				"not_in": "Not In List",
				"eq_i": "Is - Case-Insensitive",
				"ne_i": "Is Not - Case-Insensitive",
				"in_i": "Is In List - Case-Insensitive",
				"not_in_i": "Not In List - Case-Insensitive"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 1
		}
	],
	"info": "Is there a cookie set with a specific value."
},
"cookie_contains": {
	"label": "Cookie Contains",
	"category": "Visitor Metadata",
	"map": "{\"#operator\": [ \"#value\",{ \"var\":\"Cookie.#var\" } ] }",
	"lookup": ",{\\\\\"var\\\\\":\\\\\"Cookie\\\.",
	"type": "valueSubStrIndex",
	"inputs": [
		{
			"label": "Cookie Name",
			"name": "var",
			"type": "text",
			"placeholder": "Cookie Name",
			"map": 1
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"in": "Contains",
				"not_in": "Does Not Contain",
				"in_i": "Contains - Case-Insensitive",
				"not_in_i": "Does Not Contain - Case-Insensitive"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 0
		}
	],
	"info": "Does a cookie contain a specific value"
},
"server": {
	"label": "Server",
	"category": "Visitor Metadata",
	"map": "{\"#operator\": [ {\"var\":\"Server.#var\" }, \"#value\" ] }",
	"lookup": "\\\[{\\\\\"var\\\\\":\\\\\"Server\\\.",
	"type": "valueSubStrIndex",
	"inputs": [
		{
			"label": "Server Element",
			"name": "var",
			"type": "text",
			"placeholder": "Server Element",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Is",
				"!=": "Is Not",
				"in": "Is In List",
				"not_in": "Not In List",
				"eq_i": "Is - Case-Insensitive",
				"ne_i": "Is Not - Case-Insensitive",
				"in_i": "Is In List - Case-Insensitive",
				"not_in_i": "Not In List - Case-Insensitive"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 1
		}
	],
	"info": "Is there a Server element with a specific value."
},
"server_contains": {
	"label": "Server Contains",
	"category": "Visitor Metadata",
	"map": "{\"#operator\": [ \"#value\",{ \"var\":\"Server.#var\" } ] }",
	"lookup": ",{\\\\\"var\\\\\":\\\\\"Server\\\.",
	"type": "valueSubStrIndex",
	"inputs": [
		{
			"label": "Server Element",
			"name": "var",
			"type": "text",
			"placeholder": "Server Element",
			"map": 1
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"in": "Contains",
				"not_in": "Does Not Contain",
				"in_i": "Contains - Case-Insensitive",
				"not_in_i": "Does Not Contain - Case-Insensitive"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 0
		}
	],
	"info": "Does a Server element contain a specific value"
},
"custom": {
	"label": "Custom Data Object",
	"category": "Custom Data",
	"map": "{\"#operator\": [ {\"var\":\"Custom.#var\" }, \"#value\" ] }",
	"lookup": "\\\[{\\\\\"var\\\\\":\\\\\"Custom\\\.",
	"type": "valueSubStrIndex",
	"inputs": [
		{
			"label": "Variable",
			"name": "var",
			"type": "text",
			"placeholder": "Variable",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<": "Less Than",
				">=": "Greater Than Or Equal To",
				"<=": "Less Than Or Equal To",
				"!=": "Not Equal To",
				"==": "Is",
				"!=": "Is Not",
				"in": "Is In List",
				"not_in": "Not In List",
				"eq_i": "Is - Case-Insensitive",
				"ne_i": "Is Not - Case-Insensitive",
				"in_i": "Is In List - Case-Insensitive",
				"not_in_i": "Not In List - Case-Insensitive"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 1
		}
	],
	"info": "Is the custom data object set to a specific value."
},
"custom_contains": {
	"label": "Custom Data Object Contains",
	"category": "Custom Data",
	"map": "{\"#operator\": [ \"#value\",{ \"var\":\"Custom.#var\" } ] }",
	"lookup": ",{\\\\\"var\\\\\":\\\\\"Custom\\\.",
	"type": "valueSubStrIndex",
	"inputs": [
		{
			"label": "Variable",
			"name": "var",
			"type": "text",
			"placeholder": "Variable",
			"map": 1
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"in": "Contains",
				"not_in": "Does Not Contain",
				"in_i": "Contains - Case-Insensitive",
				"not_in_i": "Does Not Contain - Case-Insensitive"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 0
		}
	],
	"info": "Does the custom data object contain a value."
}
DEFAULT;

$logic['convertkit'] = <<<CONVERTKIT
"convertkit": {
	"label": "ConvertKit",
	"category": "ConvertKit",
	"map": "{\"#operator\": [ {\"var\": \"ConvertKit.email_address\" }, true ] }",
	"lookup": "ConvertKit\\\.email_address",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "User Data Available",
				"!=": "User Data Not Available"
			}
		}
	],
	"info": "Is ConvertKit data available for the current user."
},
"convertkit_tag": {
	"label": "ConvertKit Tag",
	"category": "ConvertKit",
	"map": "{\"#operator\": [ {\"key_exists\": [#tag_id, {\"var\": \"ConvertKit.tags\" }] }, true] }",
	"lookup": "ConvertKit\\\.tags",
	"type": "valueKeyExists",
	"inputs": [
		{
			"label": "Tag",
			"name": "tag_id",
			"type": "select",
			"options": "vars:convertkit_tags",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Tagged",
				"!=": "Not Tagged"
			}
		}
	],
	"info": "Has the visitor been tagged with a ConvertKit Tag."
},
"convertkit_field": {
	"label": "ConvertKit Custom Field",
	"category": "ConvertKit",
	"map": "{\"#operator\": [ {\"var\": \"ConvertKit.fields.#var\" }, \"#value\" ] }",
	"lookup": "ConvertKit\\\.fields",
	"type": "valueSubStrLastIndex",
	"inputs": [
		{
			"label": "Custom Field",
			"name": "var",
			"type": "select",
			"options": "vars:convertkit_fields",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<=": "Less Than or Equal To",
				">=": "Greater Than or Equal To",
				"<": "Less Than",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "ConvertKit Custom Field",
			"map": 1
		}
	],
	"info": "'Does the visitor have a ConvertKit Custom Field with the matching value."
}
CONVERTKIT;

$logic['drip'] = <<<DRIP
"drip": {
	"label": "Drip User Data",
	"category": "Drip",
	"map": "{\"#operator\": [ {\"var\": \"Drip.email\" }, true ] }",
	"lookup": "Drip\\\.email",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Drip user data available",
				"!=": "Drip user data not available"
			}
		}
	],
	"info": "Is Drip data available for the current user."
},
"drip_tag": {
	"label": "Drip Tag",
	"category": "Drip",
	"map": "{\"#operator\": [ {\"key_exists\": [\"#tag_id\", {\"var\": \"Drip.tags\" }] }, true] }",
	"lookup": "Drip\\\.tags",
	"type": "valueKeyExists",
	"inputs": [
		{
			"label": "Tag",
			"name": "tag_id",
			"type": "select",
			"options": "vars:drip_tags",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Tagged",
				"!=": "Not Tagged"
			}
		}
	],
	"info": "Has the visitor been tagged with a Drip Tag."
},
"drip_field": {
	"label": "Drip Custom Field",
	"category": "Drip",
	"map": "{\"#operator\": [ {\"var\": \"Drip.custom_fields.#var\" }, \"#value\" ] }",
	"lookup": "Drip\\\.custom_fields",
	"type": "valueSubStrLastIndex",
	"inputs": [
		{
			"label": "Custom Field",
			"name": "var",
			"type": "select",
			"options": "vars:drip_fields",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Is",
				"!=": "Is Not"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 1
		}
	],
	"info": "Does the visitor have a Drip Custom Field with the matching value."
}
DRIP;

$logic['woocommerce'] = <<<WOOCOMMERCE
"woocommerce_cart": {
	"label": "WooCommerce Shopping Cart",
	"category": "WooCommerce",
	"map": "{\"#operator\": [ {\"var\": \"WooCommerce.Cart\" }, true ] }",
	"lookup": "WooCommerce\\\.Cart",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Cart Has Products",
				"!=": "Cart is Empty"
			}
		}
	],
	"info": "Does the WooCommerce cart have products"
},
"woocommerce_customer_active": {
	"label": "WooCommerce Customer Data Available",
	"category": "WooCommerce",
	"map": "{\"#operator\": [ {\"var\": \"WooCommerce.Customer.Active\" }, true ] }",
	"lookup": "WooCommerce\\\.Customer\\\.Active",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Customer Data Available",
				"!=": "Customer Data Not Available"
			}
		}
	],
	"info": "Is customer data available for this user."
},
"woocommerce_customer": {
	"label": "WooCommerce Paying Customer",
	"category": "WooCommerce",
	"map": "{\"#operator\": [ {\"var\": \"WooCommerce.Customer.PayingCustomer\" }, true ] }",
	"lookup": "WooCommerce\\\.Customer\\\.PayingCustomer",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Paying Customer",
				"!=": "Not a Paying Customer"
			}
		}
	],
	"info": "Is this a paying customer."
},
"woocommerce_orders": {
	"label": "WooCommerce Orders",
	"category": "WooCommerce",
	"map": "{\"#operator\": [ {\"var\": \"WooCommerce.Customer.OrderCount\" }, \"#orders\" ] }",
	"lookup": "WooCommerce\\\.Customer\\\.OrderCount",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<=": "Less Than or Equal To",
				">=": "Greater Than or Equal To",
				"<": "Less Than",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Orders",
			"name": "orders",
			"type": "number",
			"map": 1
		}
	],
	"info": "The number of WooCommerce orders for the current customer."
},
"woocommerce_total_spend": {
	"label": "WooCommerce Total Spend",
	"category": "WooCommerce",
	"map": "{\"#operator\": [ {\"var\": \"WooCommerce.Customer.TotalSpend\" }, \"#amount\" ] }",
	"lookup": "WooCommerce\\\.Customer\\\.TotalSpend",
	"type": "default",
	"inputs": [
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<=": "Less Than or Equal To",
				">=": "Greater Than or Equal To",
				"<": "Less Than",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Amount",
			"name": "amount",
			"type": "number",
			"map": 1
		}
	],
	"info": "The number of WooCommerce orders for the current customer."
},
"woocommerce_incart": {
	"label": "WooCommerce Product In Cart",
	"category": "WooCommerce",
	"map": "{\"#operator\": [ {\"var\": \"WooCommerce.InCart.#product\" }, \"#quantity\" ] }",
	"lookup": "WooCommerce\\\.InCart\\\.",
	"type": "valueSubStrLastIndex",
	"inputs": [
		{
			"label": "Product",
			"name": "product",
			"type": "ajax",
			"source": ["product"],
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Quantity Equal To",
				">": "Quantity Greater Than",
				"<=": "Quantity Less Than or Equal To",
				">=": "Quantity Greater Than or Equal To",
				"<": "Quantity Less Than",
				"!=": "Quantity Not Equal To"
			}
		},
		{
			"label": "Quantity",
			"name": "quantity",
			"type": "number",
			"map": 1
		}
	],
	"info": "Is the product in the WooCommerce Cart."
},
"woocommerce_product": {
	"label": "WooCommerce Product Views - All Visits",
	"category": "WooCommerce",
	"map": "{\"#operator\": [ {\"var\": \"WooCommerce.Products.#product\" }, \"#views\" ] }",
	"lookup": "WooCommerce\\\.Products\\\.",
	"type": "valueSubStrLastIndex",
	"inputs": [
		{
			"label": "Product",
			"name": "product",
			"type": "ajax",
			"source": ["product"],
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<=": "Less Than or Equal To",
				">=": "Greater Than or Equal To",
				"<": "Less Than",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Views",
			"name": "views",
			"type": "number",
			"map": 1
		}
	],
	"info": "Has the product been viewed during any visit."
},
"woocommerce_product_session": {
	"label": "WooCommerce Product Views - Current Session",
	"category": "WooCommerce",
	"map": "{\"#operator\": [ {\"var\": \"WooCommerce.ProductsSession.#product\" }, \"#views\" ] }",
	"lookup": "WooCommerce\\\.ProductsSession\\\.",
	"type": "valueSubStrLastIndex",
	"inputs": [
		{
			"label": "Product",
			"name": "product",
			"type": "ajax",
			"source": ["product"],
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<=": "Less Than or Equal To",
				">=": "Greater Than or Equal To",
				"<": "Less Than",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Views",
			"name": "views",
			"type": "number",
			"map": 1
		}
	],
	"info": "Has the product been viewed this session."
},
"woocommerce_product_purchase": {
	"label": "WooCommerce Product Purchased",
	"category": "WooCommerce",
	"map": "{\"#operator\": [ {\"var\": \"WooCommerce.ProductsPurchased.#product\" }, \"#quantity\" ] }",
	"lookup": "WooCommerce\\\.ProductsPurchased\\\.",
	"type": "valueSubStrLastIndex",
	"inputs": [
		{
			"label": "Product",
			"name": "product",
			"type": "ajax",
			"source": ["product"],
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<=": "Less Than or Equal To",
				">=": "Greater Than or Equal To",
				"<": "Less Than",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Quantity",
			"name": "quantity",
			"type": "number",
			"map": 1
		}
	],
	"info": "Has the product been purchased."
},
	"woocommerce_in_category": {
	"label": "WooCommerce Product In Category",
	"category": "WooCommerce",
	"map": "{\"#operator\": [ {\"key_exists\": [#category, {\"var\": \"WooCommerce.Category\" }] }, true] }",
	"lookup": "WooCommerce\\\.Category",
	"type": "valueKeyExists",
	"inputs": [
		{
			"label": "Category",
			"name": "category",
			"type": "ajax",
			"source": ["product_cat"],
			"query": "terms",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "In Category",
				"!=": "Not In Category"
			}
		}
	],
	"info": "Is the product in a category."
},
"woocommerce_category": {
	"label": "WooCommerce Category Views - All Visits",
	"category": "WooCommerce",
	"map": "{\"#operator\": [ {\"var\": \"WooCommerce.Categories.#category\" }, \"#views\" ] }",
	"lookup": "WooCommerce\\\.Categories\\\.",
	"type": "valueSubStrLastIndex",
	"inputs": [
		{
			"label": "Category",
			"name": "category",
			"type": "ajax",
			"source": ["product_cat"],
			"query": "terms",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<=": "Less Than or Equal To",
				">=": "Greater Than or Equal To",
				"<": "Less Than",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Views",
			"name": "views",
			"type": "number",
			"map": 1
		}
	],
	"info": "Has the product category been viewed during any visit."
},
"woocommerce_category_session": {
	"label": "WooCommerce Category Views - Current Session",
	"category": "WooCommerce",
	"map": "{\"#operator\": [ {\"var\": \"WooCommerce.CategoriesSession.#category\" }, \"#views\" ] }",
	"lookup": "WooCommerce\\\.CategoriesSession\\\.",
	"type": "valueSubStrLastIndex",
	"inputs": [
		{
			"label": "Category",
			"name": "category",
			"type": "ajax",
			"source": ["product_cat"],
			"query": "terms",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<=": "Less Than or Equal To",
				">=": "Greater Than or Equal To",
				"<": "Less Than",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Views",
			"name": "views",
			"type": "number",
			"map": 1
		}
	],
	"info": "Has the product category been viewed during the current session."
},
"woocommerce_category_purchase": {
	"label": "WooCommerce Purchase From Category",
	"category": "WooCommerce",
	"map": "{\"#operator\": [ {\"var\": \"WooCommerce.CategoriesPurchased.#category\" }, \"#quantity\" ] }",
	"lookup": "WooCommerce\\\.CategoriesPurchased\\\.",
	"type": "valueSubStrLastIndex",
	"inputs": [
		{
			"label": "Category",
			"name": "category",
			"type": "ajax",
			"source": ["product_cat"],
			"query": "terms",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<=": "Less Than or Equal To",
				">=": "Greater Than or Equal To",
				"<": "Less Than",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Quantity",
			"name": "quantity",
			"type": "number",
			"map": 1
		}
	],
	"info": "Has a product been purchased from a category."
},
"woocommerce_has_tag": {
	"label": "WooCommerce Product has Tag",
	"category": "WooCommerce",
	"map": "{\"#operator\": [ {\"key_exists\": [\"#tag\", {\"var\": \"WooCommerce.Tag\" }] }, true] }",
	"lookup": "WooCommerce\\\.Tag\"",
	"type": "valueKeyExists",
	"inputs": [
		{
			"label": "Tag",
			"name": "tag",
			"type": "ajax",
			"source": ["product_tag"],
			"query": "terms",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Has Tag",
				"!=": "Does Not Have Tag"
			}
		}
	],
	"info": "Does the product have a tag."
},
"woocommerce_tag": {
	"label": "WooCommerce Tag Views - All Visits",
	"category": "WooCommerce",
	"map": "{\"#operator\": [ {\"var\": \"WooCommerce.Tags.#tag\" }, \"#views\" ] }",
	"lookup": "WooCommerce\\\.Tags\\\.",
	"type": "valueSubStrLastIndex",
	"inputs": [
		{
			"label": "Tag",
			"name": "tag",
			"type": "ajax",
			"source": ["product_tag"],
			"query": "terms",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<=": "Less Than or Equal To",
				">=": "Greater Than or Equal To",
				"<": "Less Than",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Views",
			"name": "views",
			"type": "number",
			"map": 1
		}
	],
	"info": "Has the product tag been viewed during any visit."
},
"woocommerce_tag_session": {
	"label": "WooCommerce Tag Views - Current Session",
	"category": "WooCommerce",
	"map": "{\"#operator\": [ {\"var\": \"WooCommerce.TagsSession.#tag\" }, \"#views\" ] }",
	"lookup": "WooCommerce\\\.TagsSession\\\.",
	"type": "valueSubStrLastIndex",
	"inputs": [
		{
			"label": "Tag",
			"name": "tag",
			"type": "ajax",
			"source": ["product_tag"],
			"query": "terms",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Equal To",
				">": "Greater Than",
				"<=": "Less Than or Equal To",
				">=": "Greater Than or Equal To",
				"<": "Less Than",
				"!=": "Not Equal To"
			}
		},
		{
			"label": "Views",
			"name": "views",
			"type": "number",
			"map": 1
		}
	],
	"info": "Has the product tag been viewed during the current session."
}
WOOCOMMERCE;

$logic['gravity-forms'] = <<<GRAVITYFORMS
"gravityforms_form": {
	"label": "Gravity Forms: Form Submitted",
	"category": "Gravity Forms",
	"map": "{\"#operator\": [ {\"var\": \"GravityForms.#form\" }, \"1\" ] }",
	"lookup": "GravityForms\\\.",
	"type": "valueSubStrLastIndex",
	"inputs": [
		{
			"label": "Form",
			"name": "form",
			"type": "ajax",
			"source": ["gravity-forms"],
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				">=": "Has been submitted",
				"<": "Not been submitted"
			}
		}
	],
	"info": "Has the form been submitted during any visit."
},
"gravityforms_data": {
	"label": "Gravity Forms: Field Value",
	"category": "Gravity Forms",
	"map": "{\"#operator\": [ {\"var\":\"GravityFormsData.#var\" }, \"#value\" ] }",
	"lookup": "\\\[{\\\\\"var\\\\\":\\\\\"GravityFormsData\\\.",
	"type": "valueSubStrIndex",
	"inputs": [
		{
			"label": "Variable",
			"name": "var",
			"type": "text",
			"placeholder": "Variable",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Is",
				"!=": "Is Not",
				"in": "Is In List",
				"not_in": "Not In List",
				"eq_i": "Is - Case-Insensitive",
				"ne_i": "Is Not - Case-Insensitive",
				"in_i": "Is In List - Case-Insensitive",
				"not_in_i": "Not In List - Case-Insensitive"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 1
		}
	],
	"info": "Is the Gravity Forms field set to a specific value."
},
"gravityforms_data_contains": {
	"label": "Gravity Forms: Field Value Contains",
	"category": "Gravity Forms",
	"map": "{\"#operator\": [ \"#value\",{ \"var\":\"GravityFormsData.#var\" } ] }",
	"lookup": ",{\\\\\"var\\\\\":\\\\\"GravityFormsData\\\.",
	"type": "valueSubStrIndex",
	"inputs": [
		{
			"label": "Variable",
			"name": "var",
			"type": "text",
			"placeholder": "Variable",
			"map": 1
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"in": "Contains",
				"not_in": "Does Not Contain",
				"in_i": "Contains - Case-Insensitive",
				"not_in_i": "Does Not Contain - Case-Insensitive"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 0
		}
	],
	"info": "Does the Gravity Forms field contain a value"
}
GRAVITYFORMS;

$logic['hubspot'] = <<<HUBSPOT
"hubspot_data": {
	"label": "HubSpot: Data Value",
	"category": "HubSpot",
	"map": "{\"#operator\": [ {\"var\":\"HubSpot.#var\" }, \"#value\" ] }",
	"lookup": "\\\[{\\\\\"var\\\\\":\\\\\"HubSpot\\\.",
	"type": "valueSubStrIndex",
	"inputs": [
		{
			"label": "Variable",
			"name": "var",
			"type": "text",
			"placeholder": "Variable",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Is",
				"!=": "Is Not",
				"in": "Is In List",
				"not_in": "Not In List",
				"eq_i": "Is - Case-Insensitive",
				"ne_i": "Is Not - Case-Insensitive",
				"in_i": "Is In List - Case-Insensitive",
				"not_in_i": "Not In List - Case-Insensitive"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 1
		}
	],
	"info": "Is the HubSpot variable set to a specific value."
},
"hubspot_data_contains": {
	"label": "HubSpot: Data Value Contains",
	"category": "HubSpot",
	"map": "{\"#operator\": [ \"#value\",{ \"var\":\"HubSpot.#var\" } ] }",
	"lookup": ",{\\\\\"var\\\\\":\\\\\"HubSpot\\\.",
	"type": "valueSubStrIndex",
	"inputs": [
		{
			"label": "Variable",
			"name": "var",
			"type": "text",
			"placeholder": "Variable",
			"map": 1
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"in": "Contains",
				"not_in": "Does Not Contain",
				"in_i": "Contains - Case-Insensitive",
				"not_in_i": "Does Not Contain - Case-Insensitive"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 0
		}
	],
	"info": "Does the HubSpot variable contain a value"
},
"hubspot_form": {
	"label": "HubSpot: Form Completed",
	"category": "HubSpot",
	"map": "{\"#operator\": [\"#form\", {\"var\":\"HubSpotForms\"}]}",
	"lookup": "{\\\\\"var\\\\\":\\\\\"HubSpotForms\\\\\"}",
	"type": "default",
	"inputs": [
		{
			"label": "Form Name",
			"name": "form",
			"type": "text",
			"placeholder": "Form Name",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"in_i": "Completed",
				"not_in_i": "Not Completed"
			}
		}
	],
	"info": "Has a specific HubSpot form been completed or not completed by the visitor."
}
HUBSPOT;

$logic['jabmo'] = <<<JABMO
"jabmo_data": {
	"label": "Jabmo: Data Value",
	"category": "Jabmo",
	"map": "{\"#operator\": [ {\"var\":\"Jabmo.#var\" }, \"#value\" ] }",
	"lookup": "\\\[{\\\\\"var\\\\\":\\\\\"Jabmo\\\.",
	"type": "valueSubStrIndex",
	"inputs": [
		{
			"label": "Variable",
			"name": "var",
			"type": "text",
			"placeholder": "Variable",
			"map": 0
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"==": "Is",
				"!=": "Is Not",
				"in": "Is In List",
				"not_in": "Not In List",
				"eq_i": "Is - Case-Insensitive",
				"ne_i": "Is Not - Case-Insensitive",
				"in_i": "Is In List - Case-Insensitive",
				"not_in_i": "Not In List - Case-Insensitive"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 1
		}
	],
	"info": "Is the Jabmo variable set to a specific value."
},
"jabmo_data_contains": {
	"label": "Jabmo: Data Value Contains",
	"category": "Jabmo",
	"map": "{\"#operator\": [ \"#value\",{ \"var\":\"Jabmo.#var\" } ] }",
	"lookup": ",{\\\\\"var\\\\\":\\\\\"Jabmo\\\.",
	"type": "valueSubStrIndex",
	"inputs": [
		{
			"label": "Variable",
			"name": "var",
			"type": "text",
			"placeholder": "Variable",
			"map": 1
		},
		{
			"label": "Operator",
			"name": "operator",
			"type": "select",
			"options": {
				"in": "Contains",
				"not_in": "Does Not Contain",
				"in_i": "Contains - Case-Insensitive",
				"not_in_i": "Does Not Contain - Case-Insensitive"
			}
		},
		{
			"label": "Value",
			"name": "value",
			"type": "text",
			"placeholder": "Value",
			"map": 0
		}
	],
	"info": "Does the Jabmo variable contain a value"
}
JABMO;

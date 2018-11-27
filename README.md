# Aeries-API
1). What is the Aeries URL and are they using SSL?

     Example: https://helandale.asp.aeries.net/admin

2). They need to provide us with the Aeries API "certificate" - a 32 character alpha-numeric string.

     Example: 029712efe0254k77a8e1e4821f2671cd

3). Tell them we will be hitting the following endpoints and will need "Read access" (configured on their end) for the following endpoints: schools, students, contacts, attendance, and staff. More endpoints will be needed if we plan on doing roster data (classes, teachers, courses - configured in Setup-2a).

4). If they are doing attendance we will need the codes they use (daily or period attendance is supported).

Setup:
1). Create setup folders in desired location.

2). Open file "APIAeriesInclude.php"

a). Edit "File paths" section to correct file names. If one of the files isn't being used no editing is necessary. 

b). Edit the code after "AERIES-CERT:" under "Header array for API Authentication" section to match the customer Aeries cert that they will provide us.

c). If they are not using SSL the $apiUrl variable will need https:// in the URL changed to http://

d). If they are doing attendance open the corresponding attendance PHP script (4dailyAtt.php or 4periodAtt.php) and edit $schoolCodes variable to match what school codes are doing attendance (School codes can be found from script 1). Then edit $attArray(or $periodCodes) to match what what attendance codes they are using (provided by client). 

3). Finish configuration as normal (schedule which PHP files need to run based on file name).

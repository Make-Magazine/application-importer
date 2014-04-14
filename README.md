# Maker Faire Application Importer

Allows us to take specific template of a CSV and will convert the data into a WordPress WXR file for importing exhibits or sponsors to MakerFaire.com

## How To

Clone into the root of the www folder in VIP Quickstart.

Navigate to the URL with the parameters passing the name of your CSV along with what type it is (Exhibit or Sponsor) and the URL of where to find the CSV.

	http://vip.dev/import-applications/index.php?file_name=template.csv&type=exhibit&url=http://vip.dev/import-applications/imports/

### OR

You can use CURL to process and save the output automatically. Make sure you pass the name of the CSV, the type (Exhibit or Sponsor) and the URL of where to find the CSV. One difference here is to pass the name of the outputted file.

	curl "http://vip.dev/import-applications/index.php?file_name=template.csv&type=exhibit&url=http://vip.dev/import-applications/imports/" -o "makerfaire-exibit-import.xml"

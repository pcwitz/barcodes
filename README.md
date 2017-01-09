# barcodes

Update to the Claim Search Bar Codes tool on ClaimWeb.

## Features

- Provide quick look up for receptionist to identify adjusters assigned to a claim.
- Barcode printing button for mail clerks
- Tabs for new, old, and sis with counts if results

## Requirements

- hide barcode for inactive accounts, inactive if `fnexdt !== 0`
- complete client-side html and javascript form validation
- form search inputs by
    *  Name
    *  SSN
    *  Claim #
    *  EOB #
    *  Date of Birth
    *  Date of Accident
- modern browser compatibility 
    *  javascript implementation of dymo barcode printing api: <http://developers.dymo.com/>
- DYMO Label Software 8.5.3 or newer: <http://www.dymo.com/en-US/online-support/>

## Development

- `dymo.label.framework` api: <http://labelwriter.com/software/dls/sdk/docs/DYMOLabelFrameworkJavaScriptHelp/symbols/dymo.label.framework.html>
- latest dymo javascript library: <http://www.labelwriter.com/software/dls/sdk/js/DYMO.Label.Framework.2.0.2.js>
- php iSeries backend api
- gulp
- browserify
- for development using browserify
    *  cd to claimweb and cli: `gulp watch --module barcodes`
    *  writes to `./modules/<module>/src` folder
- for production build
    *  cli: `gulp --module barcodes`
    *  build css and js into `./js` folder

## Installation

- DYMO Label Software 8.5.3 or newer: <http://www.dymo.com/en-US/online-support/>
- barcode xml
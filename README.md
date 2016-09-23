# ca.civicrm.eventbrighter
Customizations to CiviEvents to make it more like a well-known event management system.

Initial release just provides some tokens to include in your CiviMail. The tokens will be visible in the token listings, or you can manually include them. They look like this:

<code>{eventbrighter.eventbrighter[key]_[event_id]}</code>

Where key can be
<pre>
 maplink : Map Link
 registrationurl : Registration Url
 infourl : Event Information URL 
 description : Description
 summary : Summary
 title : Title
 dates : Dates 
 registration : Registration Button
 location : Location
 </pre>

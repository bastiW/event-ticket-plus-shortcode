# Shortcode for Tribe Event Ticket Plus

This WordpressPlugin adds a shortcode to  [Tribe Event Ticket Plus](https://theeventscalendar.com/product/wordpress-event-tickets-plus/).


## What it does

It allows you to use a shortcode for the ticket sale. 
![result using the shortcode wor Event Ticket Plus](https://theeventscalendar.com/content/uploads/2018/02/ad543f036fbf3112.jpg)


## Limits
- it only works if you have only one shared pool of tickets.
- Some text is hardcoded in German Language. 
- It is quick and dirty code!
- Only works with WooCommmerce

## Installation 
- Create a folder on your Wordpress Installation /wp-content/plugins/event-ticket-plus-shortcode
- Copy event-ticket-plus-shortcode.php to it
- Go the wordpress Backend and activate it

- Change the code for your needs!


## Usage

Copy the shortcode to your page or post

`[evtp-tickets id="1013" emptycart="true"]
`

`id ` - This is the id of the event

`emptycart` - Set  to `true` to clear the cart before adding the tickets to the cart. 
Set to `false` to leave the cart as it is.
Default: true

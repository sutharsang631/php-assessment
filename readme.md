# php-assessment
### shopping cart
Task   E-Commerce website using php oops and mysql 

--->ETA: 40 Hours;

--->Requirements
Create a e-commerce website using PHP OOPS and MySQL 
- Have 5 categories
- 10 products in each category with pagination
- Filter by category and price, search for products
- PLP and PDP pages
- Mini cart in PLP and PDP pages
- Cart page
-User sign in for shopping and purchase history 
- Admin sign in to view the sales report
- Use validations wherever necessary


##  solution 

Development Environment Setup: 
Set up PHP, MySQL, and Nginx for web development.

##  Database Design: 3hrs

Design and create tables for categories, products, users, orders, and sales reports. Establish appropriate relationships between entities.

## Admin Panel: 4 hrs
---->User sign in for shopping and purchase history 

----> Admin sign in to view the sales report


###  admin.php
Implement separate login and authentication mechanisms for users and admins. Allow admins to view sales reports.


## Categories and Products: 4hrs

### cart.php
Enable filtering by category and price, and implement search functionality for products.


Product Listing Page (PLP): 5hrs

---->PLP and PDP pages

----> 10 products in each category with pagination


### shop.php
Display 10 products per category with pagination and include mini cart for quick viewing.

Product Details Page (PDP): 5hrs

Show detailed product information, including images and specifications.


## searching the property : 

search the name of the product to filter the product in database table


## Shopping Cart: 3hrs


### shop.php

Implement cart functionality, allowing users to add, update, and remove items. able to see the user history

## Cart Page: 3hrs

### cart.php

----> Mini cart in PLP and PDP pages

----> Cart page

Create a cart page where users can view and edit the items in their cart.

## Place Order Process: 

### admin.php 5 hrs

---->User sign in for shopping and purchase history 

----> Admin sign in to view the sales report

Allow users to place orders, calculate total price, validate inputs, and store order details and in the database.


## final css work on it 


### `admin.php`

- Description: This file represents the admin panel of the shopping cart. It displays the orders made by users, provides pagination for better navigation, and allows the admin to mark orders as "Delivered."
- Functionality: Checks if the user is an admin, connects to the database, fetches and displays orders with user and product details, allows the admin to update order status.

### `cart.php`

- Description: This file handles the shopping cart functionality. It shows the products added to the cart, allows users to update product quantities or remove products, and provides a button to place the order.
- Functionality: Connects to the database, manages cart updates, places orders, and allows users to log out.

### `login.php`

- Description: This file handles user login and sign-up. It allows new users to sign up with a username and password and existing users to log in. Admin credentials are set up initially, and users are redirected to the shopping page after successful login.

- Functionality: Validates user credentials, sets up a new user account, and authenticates the admin user.


 ### `shop.php`

- Description: This file represents the main shopping page. It displays products with their details, offers various filters for searching and categorizing products, allows adding products to the cart, and provides pagination for better user experience.
- Functionality: Connects to the database, fetches products, applies filters for search, category, and price range, allows adding products to the cart, and enables user to see his history of purchase and  logout.
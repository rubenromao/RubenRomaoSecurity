# RubenRomaoSecurity

This Module is intended to be a test with the aim to understand the custom module that uses all the resources for an Adminhtml Custom Module.

What the module does:

1.  SQL setup script: 

    The script adds new table "admin_user_login_attempt" to the database, where the table
    contains user ID (with foreign key to admin user table), login attempt timestamp and authentication status.

    The script extends table "admin_user" with new column "locked_until" of type timestamp (nullable).

2.  Models:

    Model, resource model and resource collection to handle data from table "admin_user_login_attempt". 

3.  Custom system configuration options:

    Adds new system configuration tab called "RubenRomao" in admin panel under [System > Configuration]. 
    The tab contains section "Security".
    There are two fields:
    Is Active - dropdown with options Yes/No
    Lock For - free text input which will let us set number of seconds the account will be locked for
    
4.  Monitoring login attempts:

    Event Observer which monitor two events: 
    - Successful and Failed login attempts.
    Each listener logs information into the database by using model created in step 2.
    1. After each failure the Observer checks whether the user has exceeded 3 failed login attempts within 
    the last minute (this can be changed on the constant). 
    If so, the user admin is set to inactive user account and update "locked_until"
    column with the correct timestamp.
    
    2. Another event is triggered before admin user session authentication.
    In this event the user model is loaded and check if it is set to inactive and have "locked_until" timestamp set.
    When the lock is no longer valid the Event resets it to NULL and unlocks user's account.
        
5.  User information page:

    There is a custom block in Security module which allow to retrieve a collection
    of the last 30 login attempts for the current viewed user. 
    The block class resides under [Block/Adminhtml/Permissions/User/Edit/Tabs/LoginAttempts.php].

    Uses admin layout updates to add the new tab "Login attempts" on user information page, 
    accessible under [System > Permissions > Users].
    
    The tab type is as the custom block class which means that is to be set to load the actual template
    from [adminhtml/default/default/template/rubenromao/security/login-attempts.phtml].

    The template gets the collection of the login attempts and renders it as a simple table.

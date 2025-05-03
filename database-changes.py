import mysql.connector
import bcrypt

# MySQL connection details
db_config = {
    'host': 'localhost',         # Your MySQL host (localhost or IP)
    'user': 'root',              # Your MySQL username
    'password': '',              # Your MySQL password
    'database': 'local_service_finder', # Your MySQL database name
}

def hash_passwords():
    # Connect to MySQL database
    connection = mysql.connector.connect(**db_config)
    cursor = connection.cursor()

    try:
        # Fetch all users with plain text passwords
        cursor.execute("SELECT id, password FROM users")
        users = cursor.fetchall()

        for user in users:
            user_id = user[0]
            plain_password = user[1]

            # Hash the password using bcrypt
            hashed_password = bcrypt.hashpw(plain_password.encode('utf-8'), bcrypt.gensalt())

            # Update the password in the database with the hashed password
            update_query = "UPDATE users SET password = %s WHERE id = %s"
            cursor.execute(update_query, (hashed_password, user_id))

            # Commit the changes
            connection.commit()

            print(f"User ID {user_id}: Password hashed and updated successfully.")

    except mysql.connector.Error as err:
        print(f"Error: {err}")
    
    finally:
        # Close the database connection
        cursor.close()
        connection.close()

if __name__ == "__main__":
    hash_passwords()

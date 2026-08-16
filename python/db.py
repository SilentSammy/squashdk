import mysql.connector
from datetime import datetime, timedelta

# Database connection parameters
db_config = {
    'host': '127.0.0.1',
    'user': 'root',
    'password': '',
    'database': 'squash_dk'
}

CREDIT_VALUE = 150

def get_all_users():
    """
    Get all users from the contactos table.
    
    Returns:
        List of dictionaries containing user data
    """
    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor(dictionary=True)
        
        query = "SELECT * FROM contactos"
        
        cursor.execute(query)
        results = cursor.fetchall()
        
        cursor.close()
        conn.close()
        
        return results
        
    except mysql.connector.Error as err:
        print(f"✗ Database error: {err}")
        return []
    except Exception as e:
        print(f"✗ Error: {e}")
        return []

def get_user_id_by_whatsapp(whatsapp_number):
    """
    Get user ID and name by WhatsApp number.
    
    Args:
        whatsapp_number: WhatsApp number to search for
        
    Returns:
        Tuple of (user_id, name) if found, (None, None) otherwise
    """
    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()
        
        query = "SELECT id, nombre FROM contactos WHERE whatsapp = %s"
        
        cursor.execute(query, (whatsapp_number,))
        result = cursor.fetchone()
        
        cursor.close()
        conn.close()
        
        return (result[0], result[1]) if result else (None, None)
        
    except mysql.connector.Error as err:
        print(f"✗ Database error: {err}")
        return (None, None)
    except Exception as e:
        print(f"✗ Error: {e}")
        return (None, None)

def get_user_balance_by_whatsapp(whatsapp_number):
    """
    Get user balance by WhatsApp number.
    Calculates: sum of "Pago" activities minus sum of "Rent" activities.
    
    Args:
        whatsapp_number: WhatsApp number to search for
        
    Returns:
        Balance amount (float). Returns None if user not found or error occurs.
    """
    try:
        # Get user ID using existing function
        user_id, user_name = get_user_id_by_whatsapp(whatsapp_number)
        
        if user_id is None:
            print(f"✗ User not found for WhatsApp: {whatsapp_number}")
            return None
        
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()
        
        # Get sum of Pago activities (credits)
        pago_query = "SELECT COALESCE(SUM(valor), 0) FROM actividades WHERE contacto = %s AND tipo = 'Pago'"
        cursor.execute(pago_query, (user_id,))
        pago_total = cursor.fetchone()[0]
        
        # Get sum of Rent activities (debits)
        rent_query = "SELECT COALESCE(SUM(valor), 0) FROM actividades WHERE contacto = %s AND tipo = 'Rent'"
        cursor.execute(rent_query, (user_id,))
        rent_total = cursor.fetchone()[0]
        
        cursor.close()
        conn.close()
        
        # Calculate balance: Pago - Rent
        balance = pago_total - rent_total
        
        return balance
        
    except mysql.connector.Error as err:
        print(f"✗ Database error: {err}")
        return None
    except Exception as e:
        print(f"✗ Error: {e}")
        return None

def get_user_sessions_by_whatsapp(whatsapp_number):
    """
    Get all Rent activities for a user by WhatsApp number.
    
    Args:
        whatsapp_number: WhatsApp number to search for
        
    Returns:
        List of dictionaries with: start (fecha), duration (in minutes), room (cancha)
        Returns empty list if user not found or no activities.
    """
    try:
        # Get user ID using existing function
        user_id, user_name = get_user_id_by_whatsapp(whatsapp_number)
        
        if user_id is None:
            print(f"✗ User not found for WhatsApp: {whatsapp_number}")
            return []
        
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor(dictionary=True)
        
        # Get all Rent activities with fecha and final
        query = """
            SELECT fecha, final, cancha 
            FROM actividades 
            WHERE contacto = %s AND tipo = 'Rent'
            ORDER BY fecha DESC
        """
        
        cursor.execute(query, (user_id,))
        results = cursor.fetchall()
        
        cursor.close()
        conn.close()
        
        # Transform results to include calculated duration
        activities = []
        for row in results:
            # Calculate duration in minutes
            fecha = datetime.strptime(str(row['fecha']), '%Y-%m-%d %H:%M:%S')
            final = datetime.strptime(str(row['final']), '%Y-%m-%d %H:%M:%S')
            duration_minutes = int((final - fecha).total_seconds() / 60)
            
            activities.append({
                'start': row['fecha'],
                'duration_minutes': duration_minutes,
                'room': row['cancha']
            })
        
        return activities
        
    except mysql.connector.Error as err:
        print(f"✗ Database error: {err}")
        return []
    except Exception as e:
        print(f"✗ Error: {e}")
        return []

def add_balance(user_id, valor, descripcion='', timestamp=None):
    """
    Add a credit operation (Pago activity) for a user.
    
    Args:
        user_id: The contact/user ID
        valor: The credit amount to add
        descripcion: Description (optional)
        timestamp: Timestamp for the activity (optional, defaults to now)
    """
    try:
        # Connect to the database
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()
        
        # Current timestamp
        current_timestamp = timestamp or datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        
        # Insert credit operation (tipo='Pago' for payments/credits)
        insert_query = """
            INSERT INTO actividades 
            (contacto, fecha, final, tipo, valor, duracion, cancha, descripcion) 
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
        """
        
        values = (
            user_id,
            current_timestamp,
            current_timestamp,
            'Pago',  # 'Pago' represents credit operations
            valor,
            0,  # duracion not applicable for payments
            0,  # cancha not applicable for payments
            descripcion
        )
        
        cursor.execute(insert_query, values)
        conn.commit()
        
        print(f"✓ Credit operation inserted successfully for user {user_id}")
        print(f"  Amount: ${valor}")
        print(f"  Timestamp: {current_timestamp}")
        
        cursor.close()
        conn.close()
        
    except mysql.connector.Error as err:
        print(f"✗ Database error: {err}")
    except Exception as e:
        print(f"✗ Error: {e}")

def rent_court(user_id, duracion=0, cancha=0, descripcion='', timestamp=None):
    """
    Insert a debit operation (Rent activity) for a user.
    
    Args:
        user_id: The contact/user ID
        valor: The debit amount
        duracion: Duration (optional)
        cancha: Court number (optional)
        descripcion: Description (optional)
    """
    try:
        valor = CREDIT_VALUE * duracion  # Calculate total debit based on duration  
        # Connect to the database
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()
        
        # Current timestamp
        current_timestamp = timestamp or datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        
        # Insert debit operation (tipo='Rent' for rentals/debits)
        insert_query = """
            INSERT INTO actividades 
            (contacto, fecha, final, tipo, valor, duracion, cancha, descripcion) 
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
        """
        
        values = (
            user_id,
            current_timestamp,
            current_timestamp,
            'Rent',  # 'Rent' represents debit operations
            valor,
            duracion,
            cancha,
            descripcion
        )
        
        cursor.execute(insert_query, values)
        conn.commit()
        
        print(f"✓ Debit operation inserted successfully for user {user_id}")
        print(f"  Amount: ${valor}")
        print(f"  Timestamp: {current_timestamp}")
        
        cursor.close()
        conn.close()
        
    except mysql.connector.Error as err:
        print(f"✗ Database error: {err}")
    except Exception as e:
        print(f"✗ Error: {e}")

if __name__ == "__main__":
    # Insert a debit operation for user id 1
    # Example: $150 debit with 1 hour duration on court 1
    rent_court(
        user_id=1,
        duracion=3,
        cancha=2,
        descripcion='Rental charge',
        timestamp=datetime.now() + timedelta(hours=2)
    )
    
    # Get all users
    print("\n" + "="*50)
    print("All Users:")
    users = get_all_users()
    
    if users:
        for user in users:
            print(f"  ID: {user.get('id')} - {user.get('nombre')} ({user.get('correoElectronico')})")
    else:
        print("  No users found")
    
    # Example: Get user balance by WhatsApp number
    print("\n" + "="*50)
    print("User Balance by WhatsApp:")
    test_whatsapp = "+1234567890"  # Replace with actual WhatsApp number
    balance = get_user_balance_by_whatsapp(test_whatsapp)
    
    if balance is not None:
        print(f"  WhatsApp: {test_whatsapp}")
        print(f"  Balance: ${balance:.2f}")
    else:
        print(f"  Could not retrieve balance for {test_whatsapp}")
    
    # Example: Get user rent activities by WhatsApp number
    print("\n" + "="*50)
    print("User Rent Activities by WhatsApp:")
    activities = get_user_sessions_by_whatsapp(test_whatsapp)
    
    if activities:
        for activity in activities:
            print(f"  Start: {activity['start']}")
            print(f"    Duration: {activity['duration_minutes']} minutes")
            print(f"    Room: {activity['room']}")
            print()
    else:
        print(f"  No rent activities found for {test_whatsapp}")

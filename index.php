<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rio Guesthouse Page</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #4CAF50, #2E7D32); /* Gradient background */
            color: #495057;
            line-height: 1.6;
            background-image: url('Restro/j.jpg'); /* Replace with your image path */
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .header h1 {
            font-size: 2.5rem;
            color: #343a40;
        }
        .header .links {
            display: flex;
            gap: 20px;
        }
        .header .links a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        .header .links a:hover {
            color: #0056b3;
        }
        .hero {
            text-align: center;
            padding: 40px 0;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .hero h2 {
            font-size: 2rem;
            color: #343a40;
            margin-bottom: 10px;
        }
        .hero p {
            font-size: 1.2rem;
            color: #6c757d;
        }
        .room-photos {
            margin-top: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }
        .room-photos .room {
            width: 250px;
            margin-bottom: 20px;
            text-align: center;
        }
        .room-photos .room img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        .room-photos .room img:hover {
            transform: scale(1.05);
        }
        .room-photos .room p {
            font-size: 1rem;
            color: #495057;
            margin-top: 10px;
        }
        .reservation-form {
            margin-top: 20px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .reservation-form h2 {
            font-size: 2rem;
            color: #343a40;
            margin-bottom: 20px;
            text-align: center;
        }
        .reservation-form .form-group {
            margin-bottom: 20px;
        }
        .reservation-form .form-group input,
        .reservation-form .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 1rem;
            color: #495057;
        }
        .reservation-form .btn {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            width: 100%;
        }
        .reservation-form .btn:hover {
            background-color: #0056b3;
        }
        .about-section {
            margin-top: 20px;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center; /* Center align content */
        }
        .about-section h2 {
            font-size: 2rem;
            color: #343a40;
            margin-bottom: 20px;
        }
        .about-section p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #6c757d;
        }
        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 10px;
            }
            .header h1 {
                font-size: 2rem;
                text-align: center;
            }
            .header .links {
                justify-content: center;
            }
            .hero {
                padding: 30px 10px;
            }
            .hero h2 {
                font-size: 1.8rem;
            }
            .hero p {
                font-size: 1rem;
            }
            .room-photos .room {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Rio Guesthouse</h1>
            <div class="links">
                <a href="#">Home</a>
                <a href="restro/customer">Customer</a>
                <a href="restro/admin">Admin</a>
            </div>
        </header>

        <section class="hero">
            <h2>Welcome to Rio Guesthouse</h2>
            <p>Your perfect stay awaits in the heart of Rio de Janeiro. Book now and enjoy our luxurious rooms.</p>
            <a href="restro/Webpage" class="btn btn-primary">Book Now</a>
        </section>

        <section class="room-photos">
            <h2>Room Photos</h2>
            <div class="room">
                <a href="restro/Webpage"><img src="Restro/jp.jpg" alt="Standard Room"></a>
                <p>Standard Room - Php.300</p>
            </div>
            <div class="room">
                <a href="restro/Webpage"><img src="Restro/js.jpg" alt="Deluxe Room"></a>
                <p>Deluxe Room - Php.650</p>
            </div>
            <!-- Add more rooms as needed -->
        </section>

        <section class="reservation-form">
            <h2>Reservation Form</h2>
            <?php if (!empty($err)) { ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $err; ?>
                </div>
            <?php } ?>
            <?php if (!empty($success)) { ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $success; ?>
                </div>
                <?php if (!empty($confirmationDetails)) {
                    echo $confirmationDetails;
                } ?>
            <?php } ?>
            <form method="post" role="form">
                <div class="form-group">
                    <input class="form-control" required name="name" placeholder="Name" type="text">
                </div>
                <div class="form-group">
                    <input class="form-control" required name="email" placeholder="Email" type="email">
                </div>
                <div class="form-group">
                    <input class="form-control" required name="checkin_date" placeholder="Check-in Date" type="date">
                </div>
                <div class="form-group">
                    <input class="form-control" required name="checkout_date" placeholder="Check-out Date" type="date">
                </div>
                <div class="form-group">
                    <select class="form-control" required name="room_type">
                        <option value="">Select Room Type</option>
                        <option value="single">Single</option>
                        <option value="double">Double</option>
                        <option value="suite">Suite</option>
                    </select>
                </div>
                <div class="text-center">
                    <button type="submit" name="reserve" class="btn btn-primary my-4">Reserve</button>
                </div>
            </form>
        </section>

        <section class="about-section">
            <h2>About Us</h2>
            <p>Welcome to Rio Guesthouse, your home away from home in the heart of Rio de Janeiro. Nestled in the vibrant city center, our guesthouse offers a luxurious and comfortable stay for travelers from around the world. Whether you're here for business or leisure, our dedicated staff is committed to ensuring you have a memorable experience. Discover our beautifully appointed rooms, exceptional amenities, and personalized service that makes Rio Guesthouse the perfect choice for your stay in Rio.</p>
        </section>
    </div>
</body>
</html>

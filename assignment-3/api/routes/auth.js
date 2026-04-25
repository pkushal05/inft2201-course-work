const express = require("express");
const jwt = require("jsonwebtoken");
const users = require("../data/users");
const ApiError = require("../middleware/ApiError");

const router = express.Router();
const SECRET = process.env.JWT_SECRET;

// /login route handler
router.post("/login", (req, res, next) => {
    try {
        const { username, password } = req.body;

        // If no field provided, throw Error
        if (!username || !password) {
            return next(
                new ApiError("BadRequest", "Missing Credentials", 400),
            );
        }

        // Find the user
        const user = users.find((u) => u.username === username);

        // Match Password
        if (!user || user.password !== password) {
            return next(
                new ApiError("Unauthorized", "Invalid Credentials", 401),
            );
        }

        // If password is correct, sign a JWT
        const token = jwt.sign(
            {
                userId: user.id,
                role: user.role,
            },
            SECRET,
            { expiresIn: "1h" },
        );

        // Send the response
        res.json({ token: token });
    } catch (err) {
        next(err);
    }
});

module.exports = router;

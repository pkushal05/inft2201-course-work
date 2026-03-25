import http from "http";
import fs from "fs";
import jwt from "jsonwebtoken";
import { json } from "stream/consumers";

const JWT_SECRET =
    "ScmVkbWVhbHNpc3RlcmFjY3VyYXRlc2hpbmVzdG9wdXB3YXJkZHJpbmttYXRoZW1hdGk=";

http.createServer((req, res) => {
    if (req.method === "GET") {
        res.writeHead(200, { "Content-Type": "text/plain" });
        res.end("Hello Apache!\n");

        return;
    }

    if (req.method === "POST") {
        if (req.url === "/node/login") {
            let body = "";
            req.on("data", (chunk) => {
                body += chunk;
            });
            req.on("end", () => {
                try {
                    body = JSON.parse(body);
                    // Read the users.txt file
                    const data = fs.readFileSync("./users.txt", "utf8");

                    // Split the file into lines and search for the user
                    const lines = data.split("\n");
                    let foundUser = null;

                    for (const line of lines) {
                        // Skip empty lines
                        if (!line.trim()) continue;

                        // Format: username,password,userId,role
                        const [userId, username, password, role] =
                            line.split(",");

                        if (username === body.username) {
                            if (password === body.password) {
                                foundUser = {
                                    userId: parseInt(userId),
                                    role: role.trim(),
                                };
                                break;
                            } else {
                                // Username found, but password wrong
                                res.writeHead(401, {
                                    "Content-Type": "application/json",
                                });
                                res.end(
                                    JSON.stringify({
                                        error: "Invalid password",
                                    }),
                                );
                                return;
                            }
                        }
                    }
                    // If user not founs, Send error message.
                    if (!foundUser) {
                        res.writeHead(404, {
                            "Content-Type": "application/json",
                        });
                        res.end(
                            JSON.stringify({
                                error: `${body.username} not found`,
                            }),
                        );
                    }

                    // Built JWT token using userID and role
                    const token = jwt.sign(
                        {
                            userId: foundUser.userId,
                            role: foundUser.role,
                        },
                        JWT_SECRET,
                        { expiresIn: "1h" },
                    );

                    // Send token
                    res.writeHead(200, {
                        "Content-Type": "application/json",
                    });
                    res.end(JSON.stringify({ token: token }));
                } catch (err) {
                    console.log(err);
                    res.writeHead(500, { "Content-Type": "text/plain" });
                    res.end("Server error\n");
                }
            });
        }
        // Logout, send empty token string
        if (req.url === "/node/logout") {
            res.writeHead(500, { "Content-Type": "text/plain" });
            res.end(JSON.stringify({ token: "", message: "Logout Success" }));
        }
        return;
    }

    // Default catch
    res.writeHead(404, { "Content-Type": "text/plain" });
    res.end("Page not found! Try changing request method.");
}).listen(8000);

console.log("listening on port 8000");

<?php

class Db {
    public $conn;
    public function __construct() {
        try {
            $this->conn = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
                email TEXT,
                permission INTEGER
            )");
            $this->conn->exec("CREATE TABLE IF NOT EXISTS polls (
                id INTEGER PRIMARY KEY AUTOINCREMENT, 
                question TEXT
            )");
            $this->conn->exec("CREATE TABLE IF NOT EXISTS answers (
                id INTEGER PRIMARY KEY AUTOINCREMENT,  
                poll_id INTEGER, 
                answer TEXT
            )");
            $this->conn->exec("CREATE TABLE IF NOT EXISTS votes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,  
                poll_id INTEGER, 
                answer_id INTEGER,
                voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getUser($useremail) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email=:email");
        $stmt->bindParam(':email', $useremail);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: NULL;
    }

    public function setUser($useremail): int {
        $stmt = $this->conn->prepare("INSERT INTO users (email) VALUES (:email)");
        $stmt->bindParam(':email', $useremail);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function users(): array|null {
        $stmt = $this->conn->prepare("SELECT * FROM users");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $users ?: [];
    }

    public function getPoll($pollid) {
        $stmt = $this->conn->prepare("SELECT * FROM polls WHERE id=:id");
        $stmt->bindParam(':id', $pollid);
        $stmt->execute();
        $poll = $stmt->fetch(PDO::FETCH_ASSOC);
        return $poll['question'] ?: NULL;
    }

    public function setPoll($question): false|string {
        $stmt = $this->conn->prepare("INSERT INTO polls (question) VALUES (:question)");
        $stmt->bindParam(':question', $question);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function getAnswers($pollid): array {
        $stmt = $this->conn->prepare("SELECT * FROM answers WHERE poll_id=:poll_id");
        $stmt->bindParam(':poll_id', $pollid);
        $stmt->execute();
        $answers = $stmt->fetch(PDO::FETCH_ASSOC);
        return $answers ?: [];
    }

    public function setAnswers($pollid, $answers) {
        foreach ($answers as $answer) {
            $stmt = $this->conn->prepare("INSERT INTO answers (poll_id, answer) VALUES (:poll_id, :answer)");
            $stmt->bindParam(':poll_id', $pollid);
            $stmt->bindParam(':answer', $answer);
            $stmt->execute();
        }
    }

    public function polls(): array|null {
        $stmt = $this->conn->prepare("SELECT * FROM polls");
        $stmt->execute();
        $polls = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $polls ?: NULL;
    }

    public function getLatestPoll() {
        $stmt = $this->conn->prepare("SELECT * FROM polls ORDER BY id DESC LIMIT 1");
        $stmt->execute();
        $poll = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $poll ?: NULL;
    }

    public function getVote($user_id, $poll_id) {
        $stmt = $this->conn->prepare("SELECT * FROM votes WHERE user_id=:user_id AND poll_id=:poll_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':poll_id', $poll_id);
        $stmt->execute();
        $vote = $stmt->fetch(PDO::FETCH_ASSOC);
        return $vote;
    }

    public function setVote($userid, $pollid, $answerid) {
        $stmt = $this->conn->prepare("INSERT INTO votes (user_id, poll_id, answer_id) VALUES (:user_id, :poll_id, :answer_id)");
        $stmt->bindParam(':user_id', $userid);
        $stmt->bindParam(':poll_id', $pollid);
        $stmt->bindParam(':answer_id', $answerid);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function getVotes($poll_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(id) FROM votes WHERE poll_id=:poll_id");
        $stmt->bindParam(':poll_id', $poll_id);
        $stmt->execute();
        $votes = $stmt->fetch(PDO::FETCH_NUM);
        return $votes[0];
    }

    public function votes($poll_id = NULL): array|null {
        $stmt = $this->conn->prepare("SELECT * FROM votes  WHERE poll_id=:poll_id");
        $stmt->bindParam(':poll_id', $poll_id);
        $stmt->execute();
        $votes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $votes ?: NULL;
    }
}

class Page {
    private $page;
    public function __construct($title = '', $content = '') {
        $this->page = file_exists('template.html')
            ? file_get_contents('template.html')
            : '';
        $this->page = str_replace('{{ title }}', $title, $this->page);
        $this->page = str_replace('{{ content }}', $content, $this->page);
    }

    public function render(): void {
        echo $this->page;
    }
}

<?php


$pdo = new PDO ('mysql:dbname=api_test;host=localhost', 'homestead', 'secret');


$user = ['name' => 'testname', 'email' => 'test@email.com', 'password' => 'password', 'country_id' => 12];

$pdo->beginTransaction();

try {
    $objectUser = $pdo->prepare('INSERT INTO users(name, email, country_id, password) VALUE (:name, :email, :country_id, :password)');
    $objectUser->bindParam(':name', $name);
    $objectUser->bindParam(':email', $email);
    $objectUser->bindParam(':password', $password);
    $objectUser->bindParam(':country_id', $country_id);
    ['name' => $name, 'email' => $email, 'country_id' => $country_id, 'password' => $password] = $user;
    $objectUser->execute();

    $findUser = $pdo->prepare('SELECT id FROM users WHERE email = :userEmail');
    $findUser->bindParam(':userEmail', $user['email']);
    $findUser->execute();
    $rezult = $findUser->fetch();
    $newUserId = $rezult['id'];

    $objectUserProject = $pdo->prepare('INSERT INTO project_user(project_id, user_id) VALUE (:project_id, :user_id)');
    $projectId = rand(1, 10);
    $objectUserProject->bindParam(':user_id', $newUserId);
    $objectUserProject->bindParam(':project_id', $projectId);
    $objectUserProject->execute();

    $objectLabel = $pdo->prepare('INSERT INTO labels(name, user_id) VALUE (:name, :user_id)');
    $labelName = fake()->word();
    $objectLabel->bindParam(':name', $labelName);
    $objectLabel->bindParam(':user_id', $newUserId);
    $objectLabel->execute();

    $pdo->commit();
} catch (Exception $exception)
{
    $pdo->rollBack();
    echo $exception->getMessage().PHP_EOL;
}



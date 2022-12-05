create table logs
(
    id          int auto_increment
        primary key,
    entity_id   bigint unsigned                     not null,
    entity_name varchar(255)                        not null,
    action      enum ('create', 'update', 'delete') not null,
    content     json                                not null
);

# 1
CREATE TRIGGER `project_insert` AFTER INSERT ON `projects`
    FOR EACH ROW BEGIN
    INSERT INTO logs (entity_id, entity_name, action, content)
        VALUE (NEW.id, 'project', 'create', JSON_OBJECT('project_name', NEW.name, 'project_id', NEW.id));
end;

# 2
CREATE TRIGGER `project_update` BEFORE UPDATE ON `projects`
    FOR EACH ROW BEGIN
    INSERT INTO logs (entity_id, entity_name, action, content)
        VALUE (OLD.id, 'project', 'update', JSON_OBJECT('project_old_name', OLD.name,'project_new_name', NEW.name, 'project_id', OLD.id));
end;

# 3
CREATE TRIGGER `project_delete` BEFORE DELETE ON `projects`
    FOR EACH ROW BEGIN
    INSERT INTO logs (entity_id, entity_name, action, content)
        VALUE (OLD.id, 'project', 'delete', JSON_OBJECT('project_old_name', OLD.name, 'project_id', OLD.id));
end;


INSERT INTO projects(name) VALUE ('somename');
UPDATE projects SET name = 'different_name' WHERE id = 10;
DELETE FROM projects WHERE id = 11;

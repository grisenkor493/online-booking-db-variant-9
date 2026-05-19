USE language_center_variant9;

-- =========================================================================
-- ЗАПРОС ПО ВАРИАНТУ 9: Вывод курсов, на которые есть очередь из клиентов, прошедших тестирование
-- =========================================================================
SELECT 
    c.course_name AS 'Название курса', 
    c.language_level AS 'Уровень', 
    COUNT(e.enrollment_id) AS 'Клиентов в очереди'
FROM enrollments e
JOIN courses c ON e.course_id = c.course_id
JOIN test_results tr ON e.client_id = tr.client_id 
    AND c.course_name = tr.target_language 
    AND c.language_level = tr.recommended_level
WHERE e.status = 'в очереди' AND tr.is_passed = TRUE
GROUP BY c.course_id
HAVING COUNT(e.enrollment_id) > 0;


-- =========================================================================
-- ОБЯЗАТЕЛЬНЫЙ ЗАПРОС 1: Соединение (JOIN) минимум трёх таблиц
-- Добавлен вывод уровней курса и уровней из результатов тестирования
-- =========================================================================
SELECT 
    cl.last_name AS 'Фамилия', 
    cl.first_name AS 'Имя', 
    co.course_name AS 'Курс', 
    co.language_level AS 'Уровень курса',
    tr.recommended_level AS 'Рекомендованный уровень',
    e.status AS 'Статус записи'
FROM enrollments e
JOIN clients cl ON e.client_id = cl.client_id
JOIN courses co ON e.course_id = co.course_id
JOIN test_results tr ON cl.client_id = tr.client_id AND co.course_name = tr.target_language
ORDER BY co.course_name, e.status;


-- =========================================================================
-- ОБЯЗАТЕЛЬНЫЙ ЗАПРОС 2: Группировка с агрегатной функцией и условием HAVING
-- =========================================================================
SELECT 
    c.course_name AS 'Курс', 
    c.language_level AS 'Уровень', 
    SUM(c.price) AS 'Общий доход с курса (руб)'
FROM enrollments e
JOIN courses c ON e.course_id = c.course_id
WHERE e.status = 'зачислен'
GROUP BY c.course_id
HAVING SUM(c.price) > 20000;


-- =========================================================================
-- ОБЯЗАТЕЛЬНЫЙ ЗАПРОС 3: Запрос с подзапросом (вложенным SELECT)
-- Переделан, чтобы выводил ВСЕ записи без ограничения дат, и добавлен уровень языка
-- =========================================================================
SELECT 
    cl.last_name AS 'Фамилия', 
    cl.first_name AS 'Имя', 
    tr.target_language AS 'Язык', 
    tr.recommended_level AS 'Уровень языка',
    tr.test_date AS 'Дата теста'
FROM clients cl
JOIN test_results tr ON cl.client_id = tr.client_id;
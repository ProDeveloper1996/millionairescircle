<?php

if (!isset ($dict)) $dict = array(); 

$dict+=array 
(
//ПАГИНАЦИЯ
	"Rowsperpage" => "Выводить по",
  "Goto" => "Показывать",

// МЕНЮ
	"Menu_MyAccount" => "Мой кабинет",
	"Menu_MyMatrices" => "Мои матрицы",
	"Menu_PaymentPage" => "Страница оплат",
	"Menu_MyMatrix" => "Моя матрица",
	"Menu_SecurityPage" => "Сменить пароль",
	"Menu_Support" => "Связаться с нами",
	"Menu_MyBanners" => "Мои баннеры",
	"Menu_TellFriends" => "Поделиться с другом",
	"Menu_PaymentHistory" => "Оплаты",
	"Menu_CashHistory" => "Мой кошелек",
	"Menu_PayItForward" => "Помощь другу",
	"Menu_Withdrawals" => "Вывод средств",
	"Menu_MySite" => "Мой сайт",
	"Menu_SiteBanners" => "Системные баннеры",
	"Menu_MyTextAds" => "Моя текстовая реклама",

// ШАПКА
    "Top_Welcome" => "Добро пожаловать,",
    "Top_Logout" => "Выход",

// ЛЕВОЕ МЕНЮ
    "Left_FINANCES" => "ФИНАНСЫ",
    "Left_PROMOTION" => "РАСКРУТКА",
    "Left_AMOUNT" => "ЗАРАБОТОК",

// BOTTOM MENU
    "Sitemap" => "Карта сайта",
    "ContactUs" => "Связаться с нами",

//CASH
    "Cash_Filters" => "Фильтры",
    "Cash_Text1" => "Оплаты от ID - ",
    "Cash_Apply" => "Применить",
    "Cash_ListEmpty" => "Список пуст.",
    "Cash_pageTitle" => "Кошелек",
    "Cash_Datefrom" => "Даты от ",
    "Cash_to" => " до ",
    "Cash_Totalamount" => "Общая сумма",
    "Cash_MakeWithdrawalRequest" => "Запрос на вывод",
    "Cash_Amount" => "Сумма",
    "Cash_Date" => "Дата",
    "Cash_Description" => "Описание",
    "Cash_Level" => "Уровень",
    "Cash_Number" => "Номер",
    "Cash_From" => "От",
    "Cash_cmx" => "заполненная матрица",
    "Cash_cmx1" => "уровня",

//Cash Out
    "CashO_pageTitle" => "Страница запросов на вывод",
    "CashO_Text1" => "Ваш запрос на вывод средств успешно зарегистрирован",
    "CashO_Process" => "Запросить",
    "CashO_Cancel" => "Отмена",
    "CashO_NoCash" => "В Вашем кошельке нет средств",
    "CashO_error1" => "Сумма на вывод",
    "CashO_error2" => "Недостаточно средств для вывода данной суммы.Введите корректную сумму.",
    "CashO_error3" => "Минимальная сумма для вывода.",
    "CashO_error4" => "Введите корректную сумму.",

//m_level
	"ML_pageTitle" => "Уровень: ",
	"ML_members" => "пользователей",
	"ML_ShowDetails" => "Показать детали",
	"ML_Nolevels" => "No levels",
	"ML_ShowMatrixTree" => "Показать все дерево",

//MATRIX
  "MX_Title" => "Matrix",
	"MX_pageTitle" => "Матрица",
	"MX_NotEn" => "Данный уровень не открыт.",
	"MX_Level" => "Уровень",
	"MX_Completed" => "Завершена",
	"MX_Status" => "Статус",
	"MX_Incompleted" => "Незавершенная",

//MYACCOUNT
    "MyAcc_RegistrationDate" => "Дата регистрации",
    "MyAcc_LastAccess" => "Последний вход",
    "MyAcc_YourAccountID" => "ID вашего аккаунта",
    "MyAcc_YourReferralLink" => "Ваша реферальная ссылка",
    "MyAcc_YourEnrollerID" => "ID вашего спонсора",
    "MyAcc_SponsoredMembers" => "Приглашенных пользователей",
    "MyAcc_Details" => "Подробнее",
    "MyAcc_YourLevel" => "Ваш уровень",
    "MyAcc_Amountearned" => "Заработано",
    "MyAcc_FirstName" => "Имя",
    "MyAcc_LastName" => "Фамилия",
    "MyAcc_EmailAddress" => "Email",
    "MyAcc_Username" => "Логин",
    "MyAcc_Password" => "Пароль",
    "MyAcc_Text1" => "Открыть страницу безопасности",
    "MyAcc_Update" => "Сохранить",
    "MyAcc_Address" => "Адрес",
    "MyAcc_City" => "Город",
    "MyAcc_State" => "Область",
    "MyAcc_Country" => "Страна",
    "MyAcc_PostalCode" => "Почтовый индекс",
    "MyAcc_Phone" => "Телефон",
    "MyAcc_PaymentProcessor" => "платежный процессор",
    "MyAcc_AccountID" => "ID аккаунта",
    "MyAcc_pageTitle" => "Профиль",
    "MyAcc_Overview" => "Обзор",
    "MyAcc_Accesssettings" => "Доступ",
    "MyAcc_AddressSettings" => "Адрес",
    "MyAcc_PaymentSettings" => "Плтаженая система",
    "MyAcc_Text2" => "Изменения аккаунта сохранены<br /><br />",
    "MyAcc_Text3" => "Новый пароль был выслан вам на почту<br /><br />",
    "MyAcc_Authorized" => "Одобрено",
    "MyAcc_NotAuthorized" => "На рассмотрении",
    "MyAcc_reflink" => "Для получения реферальной ссылки требуется [ <a class='smallLink' href='payment.php'>более высокий уровень</a> ]",
    "MyAcc_Upgradelevel" => "Поднять уровень",
    "MyAcc_LandingPages" => "Целевые страницы: ",
    "MyAcc_Text4" => "In pay period.<br>Оплата должны быть совершена до ",
    "MyAcc_Text5" => ", чтобы избежать удаления.",
    "MyAcc_Text6" => "Неоплаченный.<br>ваш аккаунт будет удален ",
    "MyAcc_Text7" => "в случае, если оплата не будет совершена.",
    "MyAcc_YourStatus" => "YВаш статус",
    "MyAcc_Downlinemembers" => "Downline members :",
    "MyAcc_Text8" => "Вы находитесь наверху системы",
    "MyAcc_Coded" => "Закодировано",
    "MyAcc_Selectprocessor" => "Выберите процессор",
    "MyAcc_AccountID" => "ID аккаунта",
    "MyAcc_UrlofMySite" => "URL сайта",
    "MyAcc_Text9" => "Данный URL уже зарегистрирован в системе. Выберите другой.",
    "MyAcc_Text10" => "Данный адрес почты уже используется в системе. Выберите другой.",
    "MyAcc_Text11" => "Выберите способ оплаты.",

// ticket_new
    "TN_Subject" => "Тема",
    "TN_Message" => "Текст",
    "TN_Submit" => "Отправить",
    "TN_Cancel" => "Отмена",
    "TN_Actions" => "Действия",
    "TN_ListEmpty" => "Запросов нет.",
    "TN_LastUpdate" => "Последнее изменение",
    "TN_Status" => "Статус",
    "TN_CreatedOn" => "Создан",
    "TN_Postedon" => "Отправлен",
    "TN_pageTitle" => "Список запросов",
    "TN_mess1" => "Сбой в системе. Запрос не может быть отправлен. Повторите попытку позже.",
    "TN_CreateTicket" => "Создать запрос",
    "TN_Datecreate" => "Дата создания",
    "TN_Lastupdate" => "последнее изменение",
    "TN_Lastreplier" => "Последний ответил",
    "TN_mess2" => "Изменить статус активности",
    "TN_Details" => "Подробнее",
    "TN_Del" => "Вы действительно хотите удалить запись?",
    "TN_Delete" => "Удалить",
    "TN_Alltickets" => "Все запросы",
    "TN_Closedtickets" => "Завершенные запросы",
    "TN_Opentickets" => "Активные запросы",
    "TN_pageTitle1" => "Детали запроса",
    "TN_mess3" => "Сбой в системе. Ваш ответ не может быть отправлен. Повторите попытку позже.",
    "TN_Open" => "Активен",
    "TN_Close" => "Закрыт",
    "TN_Reply" => "Ответить",
    "TN_pageTitle2" => "Создать запрос",
    "TN_pageTitle3" => "Ответить на запрос",

//NEWS 
    "News_NoNews" => "Новостей нет.",
    "News_pageTitle" => "Новости",
    "News_pageTitleDet" => "Подробнее",
    "News_Clicktoenlarge" => "Нажмите для увеличения",
    "News_AllNews" => "Все новости",
    "News_LatestNews" => "Последние новости",

// PAGES
    "Page_PageTitle" => "Название страницы",
    "Page_NameMenu" => "Заголовок страницы в меню",
    "Page_Content" => "Содержание",
    "Page_Update" => "Сохранить",
    "Page_Cancel" => "Отмена",
    "Page_ShowMySite" => "Показывать мой сайт",
    "Page_Actions" => "Действия",
    "Page_ListEmpty" => "Список пуст.",
    "Page_UrlofMySite" => "URL моего сайта",

//PAYMENT
    "PM_Filter" => "Фильтр",
    "PM_Apply" => "Применить",
    "PM_ListEmpty" => "Оплат не совершалось.",
    "PM_pageTitle" => "Страница оплат",
    "PM_MembershipFee" => "Комиссия",
    "PM_mess1" => "Выберите процессор и уровень",
    "PM_mess2" => "На вашем балансе недостаточно средств",
    "PM_Text1" => "Ваш статус Активный. Теперь вы можете продлевать ваш текущий уровень. Также вы можете поднять уровень после ",
    "PM_Selectprocessor" => "Выбериет процессор",
    "PM_mess3" => "С моего баланса (баланс: ",
    "PM_fee" => "комиссия",
    "PM_Selectlevel" => "Выберите уровень...",
    "PM_cost" => "Стоимость",
    "PM_pageTitle1" => "Страница оплаты: Предпросмотр",
    "PM_Payment_for_level" => "Оплата за уровень",
    "PM_mess4" => "Оплата с внутреннего баланса",
    "PM_pageTitle2" => "Статус оплаты",
    "PM_mess5" => "Спасибо. Ваша оплата прошла успешно.",
    "PM_mess6" => "Оплата была аннулирована.",
    "PM_pageTitle3" => "Оплаты",
    "PM_Datefrom" => "Даты с",
    "PM_to" => " до ",
    "PM_Amount" => "Сумма",
    "PM_Date" => "Дата",
    "PM_TransactionID" => "ID транзакции",
    "PM_Processor" => "Процессор",
    "PM_mess7" => "Оплата с внутреннего баланса",
    "PM_Manual" => "Ручная оплата",

// PTOOLS
    "PT_NewBannerForm" => "Создание баннера",
    "PT_Title" => "Название",
    "PT_Image" => "Изображение",
    "PT_SelectPageForPromotion" => "Выберите страницу для раскрутки",
    "PT_AddBanner" => "Добавить баннер",
    "PT_ListEmpty" => "Список пуст.",
    "PT_pageTitle" => "Баннеры",
    "PT_mess1" => "Баннер добавлен",
    "PT_mess2" => "Баннер удален",
    "PT_mess3" => "Sсистемная ошибка. Повторите попытку позже.",
    "PT_mess4" => "Вы действительно хотите удалить баннер?",
    "PT_Delete" => "Удалить",
    "PT_errorphoto" => "Выберите другое изображение.",
    "PT_errorphoto1" => "Неподходящий формат файла. Выберите другое изображение.",

// TAD
    "TD_Title" => "Название",
    "TD_Title1" => "(не более 25 символов)",
    "TD_Description" => "Описание 1",
    "TD_Description1" => "(не более 35 символов)",
    "TD_Description2" => "Описание 2",
    "TD_Description3" => "(не более 35 символов)",
    "TD_URL" => "URL",
    "TD_URL1" => "(Например: http://www.google.com/)",
    "TD_ShowURLinZone" => "Показывать URL",
    "TD_Update" => "Сохранить",
    "TD_Cancel" => "Отмена",
    "TD_Text1" => "Пользователи могут рекламировать свои услуги",
    "TD_Text2" => "Данная услуга бесплатная.",
    "TD_Text3" => "Реклама появляется на всех страницах",
    "TD_Text4" => "На публичных страницах",
    "TD_Text5" => "реклам за раз на страницах логина",
    "TD_Text6" => "текстовых реклам.",
    "TD_hTitle" => "Название",
    "TD_hContent" => "Содержание",
    "TD_hDisplayed" => "Показано",
    "TD_hAction" => "Действия",
    "TD_ListEmpty" => "Список пуст.",
    "TD_pageTitle" => "Моя текстовая реклама",
    "TD_add" => "Добавить текстовую рекламу",
    "TD_del" => "Вы действительно хотите удалить запись?",
    "TD_pageTitle1" => "Новая текстовая реклама",
    "TD_Title" => "Название",
    "TD_pageTitle2" => "Редактировать",

// TellFriend
    "TF_Text1" => "Детали сообщения для отправки",
    "TF_Subject" => "Тема",
    "TF_Message" => "Письмо",
    "TF_Sendingform" => "Форма для рассылки",
    "TF_Sendto" => "Послать",
    "TF_Firstname" => "Имя",
    "TF_Lastname" => "Фамилия",
    "TF_Email" => "Email",
    "TF_Send" => "Отправить",
    "TF_confirm" => "Вы уверены?",
    "TF_pageTitle" => "Рассказать другу",
    "TF_mess1" => "Ваше приглашение было отправлено ",
    "TF_mess2" => " друзьям.",
    "TF_mess3" => "Укажите хотя бы один адрес.",

//Replica Site
    "RS_pageTitle" => "Мой сайт",
    "RS_Authorized" => "Одобрен",
    "RS_NotAuthorized" => "На рассмотрении",
    "RS_mess1" => "URL вашего сайта: ",
    "RS_mess2" => "Сайты еще не были добавлены",
    "RS_add" => "Добавить страницу",
    "RS_mess3" => "Вы не можете больше добавлять страницы.",
    "RS_Order" => "Порядок",
    "RS_Name" => "Название страницы",
    "RS_Title" => "Название ссылки в разделе меню",
    "RS_MoveUp" => "Поднять",
    "RS_MoveDown" => "Опустить",
    "RS_Changeactivitystatus" => "Изменить статус активности",
    "RS_Edit" => "Редактировать",
    "RS_Del" => "Вы действительно хотите удалить эту страницу?",
    "RS_Delete" => "Удалить",
    "RS_pageTitle1" => "Страницы моего сайта",
    "RS_UrlofMySite" => "URL моего сайта",
    "RS_error" => "Такой URL уже существует. Выберите другой.",
    "RS_pageTitle2" => "Новая страница",
    "RS_err1" => "Название",
    "RS_pageTitle3" => "Редактировать",

//withdrawal 
    "WD_pageTitle" => "Запросы на вывод денег",
    "WD_Amount" => "Сумма",
    "WD_Date" => "Дата",
    "WD_Status" => "статус",
    "WD_Processor" => "Процессор",
    "WD_AccountID" => "ID аккаунта",
    "WD_FEE" => "Комисия",

        'statusList' => array(
        "0" => "Ожидание", "1" => "Завершено", "2" => "Отклонено"
    ),

	"RS_INFO"=>'Информация',

);

?>

use nerdygadgets;
-- table structure
drop table if exists discountCodes;
create table if not exists discountCodes(
	DiscountID int primary key,
    CustomerID int,
    DiscountCode varchar(10) not null,
    DiscountAmount decimal(4,2) default null,
    DiscountPercentage decimal(5,2) default null,
    DiscountShipping decimal(6,2) default null,
    CodeDescription varchar(200) not null,
    StartDate date not null,
    EndDate date not null,
    Expired BOOL,
    LastEditedBy int not null,
    LastEditedWhen datetime not null default current_timestamp(),
    FOREIGN key (CustomerID) references customers(CustomerID),
    foreign key (LastEditedBy) references people(PersonID)

);
-- indexes
create index idx_DiscountID
on discountCodes(DiscountID);

-- triggers
-- zorgen ervoor dat de last edit when altijd accuraat blijven
delimiter //
create trigger Update_lastEdit
before update on discountCodes
for each row
BEGIN
	set new.LastEditedWhen = current_timestamp();
END; //

create trigger Insert_lastEdit
before insert on discountCodes
for each row
begin
	set new.LastEditedWhen = current_timestamp(); 
END;//

create trigger insert_expired
before insert on discountCodes
for each row
BEGIN
	if new.EndDate<=current_timestamp() then
		set new.Expired = 1;
    else set 
		new.Expired = 0;
    end if;
END; //

create trigger update_expired
before update on discountCodes
for each row
BEGIN
	if new.EndDate<=current_timestamp() then
		set new.Expired = 1;
    else
		set new.Expired = 0;
    end if;
END; //

delimiter ;

-- test values
insert into discountCodes
values
(1,null,"Test%",null,10,null,"Testcode percentage","2021-12-13","2069-04-20",0,1,current_timestamp()),
(2,null,"Test$",10,null,null,"Testcode hoeveelheid","2021-12-13","2069-04-20",0,1,current_timestamp()),
(3,null,"Test_klant",null,15,null,"Testcode klant specifiek","2021-12-13","2069-04-20",0,1,current_timestamp()),
(4,null,"Test_v",null,null,100,"Gratis verzendkosten, verkregen via mail","2021-12-13","2069-04-20",0,1,current_timestamp()),
(5,null,"N72RE3Z2ZU",null,15,null,"15% korting, verkregen bij het registreren","2021-12-13","2069-04-20",0,1,current_timestamp()),
(6,null,"YANF7B4OM9",null,null,100,"Gratis verzendkosten, verkregen via mail","2021-12-13","2019-04-20",0,1,current_timestamp());
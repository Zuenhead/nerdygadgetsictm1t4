use nerdygadgets;
-- table structure
drop table if exists discountCodes;
create table if not exists discountCodes(
	DiscountID int primary key auto_increment,
    CustomerID int,
    DiscountCode varchar(10) not null unique,
    DiscountAmount decimal(5,2) default null,
    DiscountPercentage decimal(5,2) default null,
    DiscountShipping decimal(5,2) default null,
    CodeDescription varchar(200) not null,
    StartDate date not null,
    EndDate date not null,
    Expired BOOL,
    LastEditedBy int not null,
    LastEditedWhen datetime not null default current_timestamp(),
    FOREIGN key (CustomerID) references useraccounts(PersonID),
    foreign key (LastEditedBy) references people(PersonID)
);
-- indexes
create index idx_DiscountCode
on discountCodes(DiscountCode);

-- test values
insert into discountCodes
values
(1,null,"N72RE3Z2ZU",null,15,null,"15% korting, verkregen bij promotie","2021-12-13","2069-04-20",0,1,current_timestamp()),
(2,null,"XHZUQVDSWQ",10,null,null,"€10 korting, verkregen bij promotie","2021-12-13","2069-04-20",0,1,current_timestamp()),
(3,null,"YANF7B4OM9",null,null,100,"Gratis verzendkosten, verkregen via mail","2021-12-13","2019-04-20",0,1,current_timestamp());
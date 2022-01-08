use nerdygadgets;
drop user if exists bezoeker;
drop user if exists klant;


-- users aanmaken 
create user "bezoeker" identified by "bezoeker";
create user "klant" identified by "betalendeBezoeker";


-- permissions voor bezoekers
grant select on stockitems to bezoeker;
grant select, update on stockitemholdings to bezoeker;
grant select on stockitemimages to bezoeker;
grant select, insert on neworders to bezoeker;
grant select, insert on neworderlines to bezoeker;
grant select on discountCodes to bezoeker;
grant select on reviews to bezoeker;
grant select, insert on useraccounts to bezoeker;
grant select on cities to bezoeker;
grant select on coldroomTemperatures to bezoeker;
grant select on stockgroups to bezoeker;
grant select on stockitemstockgroups to bezoeker;

-- privileges klanten 
grant select on stockitems to klant;
grant select, update on stockitemholdings to klant;
grant select on stockitemimages to klant;
grant select, insert on neworders to klant;
grant select, insert on neworderlines to klant;
grant select on discountCodes to klant;
grant select, insert on reviews to klant;
grant select, insert, update on useraccounts to klant;
grant select on cities to klant;
grant select on coldroomTemperatures to klant;
grant select on stockgroups to klant;
grant select on stockitemstockgroups to klant;






 
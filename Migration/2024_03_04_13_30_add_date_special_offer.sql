alter table up_special_offer
	add start_date date null;

alter table up_special_offer
	add end_date date null;

UPDATE eshop.up_special_offer t SET t.start_date = '2024-03-01', t.end_date = '2024-03-31' WHERE t.id = 1;

alter table up_special_offer
	modify start_date date not null;

alter table up_special_offer
	modify end_date date not null;
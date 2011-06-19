insert into disc (label)
select file.disc from file group by file.disc;

select * from disc;

update disc,file set file.disc_id=disc.id where disc.formatlabel=file.disc;

select * from file limit 10;

select disc.label,file.dir,file.file from disc inner join file
on disc.id = file.disc_id
where disc.label like 'software-1';

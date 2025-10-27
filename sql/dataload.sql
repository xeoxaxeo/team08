-- dataload.sql: 테이블에 .csv 데이터 로드 -> dbinsert.sql 생성용

USE team08;

SET FOREIGN_KEY_CHECKS=0;

-- Master
LOAD DATA INFILE 'C:/ProgramData/MySQL/MySQL Server 8.0/Uploads/Master.csv'
INTO TABLE master
FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
(@playerID, @birthYear, @birthMonth, @birthDay, @birthCountry, @birthState, @birthCity, @deathYear, @deathMonth, @deathDay, @deathCountry, @deathState, @deathCity, @nameFirst, @nameLast, @nameGiven, @weight, @height, @bats, @throws, @debut, @finalGame, @retroID, @bbrefID)
SET playerID = @playerID, birthYear = IF(@birthYear = '', 0, @birthYear), nameFirst = @nameFirst, nameLast = @nameLast, bats = @bats, throws = @throws;

-- Teams
LOAD DATA INFILE 'C:/ProgramData/MySQL/MySQL Server 8.0/Uploads/Teams.csv'
INTO TABLE teams
FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
(@yearID, @lgID, @teamID, @franchID, @divID, @Rank, @G, @Ghome, @W, @L, @DivWin, @WCWin, @LgWin, @WSWin, @R, @AB, @H, @2B, @3B, @HR, @BB, @SO, @SB, @CS, @HBP, @SF, @RA, @ER, @ERA, @CG, @SHO, @SV, @IPouts, @HA, @HRA, @BBA, @SOA, @E, @DP, @FP, @name, @park, @attendance, @BPF, @PPF, @teamIDBR, @teamIDlahman45, @teamIDretro)
SET yearID = @yearID, lgID = @lgID, teamID = @teamID, name = @name, W = IF(@W = '', 0, @W), L = IF(@L = '', 0, @L), `Rank` = IF(@Rank = '', 0, @Rank);

-- Batting
LOAD DATA INFILE 'C:/ProgramData/MySQL/MySQL Server 8.0/Uploads/Batting.csv'
INTO TABLE batting
FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
(@playerID, @yearID, @stint, @teamID, @lgID, @G, @AB, @R, @H, @2B, @3B, @HR, @RBI, @SB, @CS, @BB, @SO, @IBB, @HBP, @SH, @SF, @GIDP)
SET playerID = @playerID, yearID = @yearID, stint = @stint, teamID = @teamID, lgID = @lgID, G = IF(@G = '', 0, @G), AB = IF(@AB = '', 0, @AB), H = IF(@H = '', 0, @H), HR = IF(@HR = '', 0, @HR);

-- Pitching
LOAD DATA INFILE 'C:/ProgramData/MySQL/MySQL Server 8.0/Uploads/Pitching.csv'
INTO TABLE pitching
FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
(@playerID, @yearID, @stint, @teamID, @lgID, @W, @L, @G, @GS, @CG, @SHO, @SV, @IPouts, @H, @ER, @HR, @BB, @SO, @BAOpp, @ERA, @IBB, @WP, @HBP, @BK, @BFP, @GF, @R, @SH, @SF, @GIDP)
SET playerID = @playerID, yearID = @yearID, stint = @stint, teamID = @teamID, lgID = @lgID, W = IF(@W = '', 0, @W), L = IF(@L = '', 0, @L), G = IF(@G = '', 0, @G), ERA = IF(@ERA = '', NULL, @ERA);

-- Fielding
LOAD DATA INFILE 'C:/ProgramData/MySQL/MySQL Server 8.0/Uploads/Fielding.csv'
INTO TABLE fielding
FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
(@playerID, @yearID, @stint, @teamID, @lgID, @POS, @G, @GS, @InnOuts, @PO, @A, @E, @DP, @PB, @WP, @SB, @CS, @ZR)
SET playerID = @playerID, yearID = @yearID, stint = @stint, teamID = @teamID, lgID = @lgID, POS = @POS;

-- Salaries
LOAD DATA INFILE 'C:/ProgramData/MySQL/MySQL Server 8.0/Uploads/Salaries.csv'
INTO TABLE salaries
FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
( @yearID, @teamID, @lgID, @playerID, @salary )
SET playerID = @playerID, yearID = @yearID, teamID = @teamID, lgID = @lgID, salary = IF(@salary = '', 0, @salary);

-- AllstarFull
LOAD DATA INFILE 'C:/ProgramData/MySQL/MySQL Server 8.0/Uploads/AllstarFull.csv'
INTO TABLE allstarfull
FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
(@playerID, @yearID, @gameNum, @gameID, @teamID, @lgID, @GP, @startingPos)
SET playerID = @playerID, yearID = @yearID, gameNum = @gameNum, gameID = @gameID, teamID = @teamID, lgID = @lgID, startingPos = IF(@startingPos = '', NULL, @startingPos);

SET FOREIGN_KEY_CHECKS=1;
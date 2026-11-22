ALTER TABLE placement
  DROP PRIMARY KEY,
  MODIFY adoptionid int DEFAULT NULL,
  MODIFY fosterid int DEFAULT NULL,
  ADD PRIMARY KEY (placementid);

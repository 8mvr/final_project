-- Seed Data matching the Figma "Hearth" Pet Adoption design system
USE `petadoptiondb`;

-- Add sample staff and applicant accounts if not present
INSERT IGNORE INTO `user` (`userid`, `full_name`, `email`, `password`, `role`) VALUES
(1, 'Priya Natarajan', 'priya@hearth.example', '$2y$10$99vT0LA9b9o.HPd/doiNTO.w0tUDxLqr/n3O/.Nfd2PmEFdC1DnxW', 'staff'),
(2, 'Alex Rivera', 'alex.rivera@example.com', '$2y$10$99vT0LA9b9o.HPd/doiNTO.w0tUDxLqr/n3O/.Nfd2PmEFdC1DnxW', 'user');

-- Insert pets matching Figma mockups
INSERT INTO `pet` (`petid`, `created_by`, `name`, `species`, `breed`, `age`, `sex`, `description`, `photo_url`, `status`) VALUES
(1, 1, 'Maple', 'Dog', 'Beagle mix', 3, 'Female', 'A sunny patch on the floor, a slow sniff around the block, and someone to lean against. Gentle little shadow.', 'assets/images/pet-maple.png', 'Available'),
(2, 1, 'Milo', 'Cat', 'Domestic Shorthair', 2, 'Male', 'Loves watching birds by the window and greeting you with soft chirps in the morning.', 'assets/images/pet-milo.png', 'Available'),
(3, 1, 'Luna', 'Cat', 'Tabby mix', 1, 'Female', 'Curious explorer who loves chasing feather wands and curling up in warm laps.', 'assets/images/pet-luna.png', 'Foster welcome'),
(4, 1, 'Finn', 'Dog', 'Golden Retriever', 1, 'Male', 'Always ready for an outdoor hike or game of fetch, full of friendly enthusiasm.', 'assets/images/pet-finn.png', 'Available'),
(5, 1, 'Juniper', 'Cat', 'Shorthair', 4, 'Female', 'Currently thriving in foster care, enjoys gentle head scratches and sunny naps.', 'assets/images/pet-juniper.png', 'In foster'),
(6, 1, 'Archie', 'Dog', 'Spaniel', 6, 'Male', 'Gentleman companion with a calm disposition, great with kids and other dogs.', 'assets/images/pet-archie.png', 'Available'),
(7, 1, 'Pepper', 'Rabbit', 'Lop Rabbit', 2, 'Female', 'Gentle rabbit who loves fresh hay, leafy greens, and quiet companionship.', 'assets/images/pet-pepper.png', 'Available')
ON DUPLICATE KEY UPDATE 
    `name`=VALUES(`name`),
    `species`=VALUES(`species`),
    `breed`=VALUES(`breed`),
    `age`=VALUES(`age`),
    `sex`=VALUES(`sex`),
    `description`=VALUES(`description`),
    `photo_url`=VALUES(`photo_url`),
    `status`=VALUES(`status`);

-- Insert sample applications
INSERT INTO `adoption` (`adoptionid`, `userid`, `petid`, `reviewed_by`, `application_date`, `status`, `reason`) VALUES
(1, 2, 1, 1, '2026-11-03', 'Under review', 'Looking for an easygoing companion dog for a quiet home.')
ON DUPLICATE KEY UPDATE `status`=VALUES(`status`);

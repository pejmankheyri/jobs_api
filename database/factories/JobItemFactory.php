<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobItemFactory extends Factory
{

    public $exampleITJobTitles = [
        'Software Engineer',
        'Frontend Developer',
        'Backend Developer',
        'Fullstack Developer',
        'DevOps Engineer',
        'Data Scientist',
        'Machine Learning Engineer',
        'QA Engineer',
        'Product Manager',
        'Project Manager',
        'Scrum Master',
        'CTO',
        'CEO',
        'COO',
        'CFO',
        'CMO',
        'HR Manager',
        'Recruiter',
        'Sales Manager',
        'Marketing Manager',
        'Customer Support',
        'Customer Success',
        'Account Manager',
        'Business Development Manager',
        'UX Designer',
        'UI Designer',
        'Graphic Designer',
        'Content Writer',
        'SEO Specialist',
        'PPC Specialist',
        'Social Media Manager',
        'Data Analyst',
        'Business Analyst',
        'Financial Analyst',
        'Legal Counsel',
        'Office Manager',
        'System Administrator',
        'Network Administrator',
        'IT Support Specialist',
        'IT Manager',
        'IT Director',
        'Security Specialist',
        'Database Administrator',
        'Cloud Architect',
        'Cloud Engineer',
        'Network Engineer',
        'System Engineer',
        'Technical Support',
        'Technical Account Manager',
        'Technical Project Manager',
        'Technical Writer',
        'Technical Recruiter',
        'Technical Trainer',
        'Technical Lead',
        'Technical Director',
        'Technical Architect',
        'Technical Evangelist',
        'Technical Product Manager',
        'Technical Program Manager',
        'Technical Solutions Architect',
        'Technical Support Engineer',
        'Technical Support Specialist',
        'Technical Systems Analyst',
        'Technical Team Lead',
        'Technical Writer',
        'Technical Specialist',
        'Technical Services Manager',
        'Technical Services Engineer',
        'Technical Services Specialist',
        'Technical Services Analyst',
        'Technical Services Consultant',
        'Technical Services Coordinator',
        'Technical Services Director',
        'Technical Services Representative',
        'Technical Services Supervisor',
        'Technical Services Technician',
        'Technical Services Administrator',
        'Technical Services Associate',
        'Technical Services Assistant',
        'Technical Services Coordinator',
        'Technical Services Manager',
        'Technical Services Officer',
        'Technical Services Specialist',
        'Technical Services Supervisor',
        'Technical Services Technician',
        'Technical Services Administrator',
        'Technical Services Associate',
        'Technical Services Assistant',
        'Technical Services Coordinator',
        'Technical Services Director',
        'Technical Services Representative',
    ];

    public $exampleITJobDescriptions = [
        'We are looking for a Software Engineer to join our team. You will work on various projects and tasks, such as developing software, maintaining systems, and analyzing data. You should have a strong background in computer science and programming, as well as experience with software development tools and technologies. You should also have excellent problem-solving skills and the ability to work well in a team environment. If you are passionate about technology and want to make a difference in the world, we would love to hear from you!',
        'We are looking for a Frontend Developer to join our team. You will work on various projects and tasks, such as designing user interfaces, developing web applications, and optimizing websites for performance. You should have a strong background in web development and programming, as well as experience with frontend technologies and tools. You should also have excellent problem-solving skills and the ability to work well in a team environment. If you are passionate about technology and want to make a difference in the world, we would love to hear from you!',
        'We are looking for a Backend Developer to join our team. You will work on various projects and tasks, such as developing server-side applications, optimizing databases, and integrating third-party services. You should have a strong background in backend development and programming, as well as experience with backend technologies and tools. You should also have excellent problem-solving skills and the ability to work well in a team environment. If you are passionate about technology and want to make a difference in the world, we would love to hear from you!',
        'We are looking for a Fullstack Developer to join our team. You will work on various projects and tasks, such as designing user interfaces, developing web applications, optimizing databases, and integrating third-party services. You should have a strong background in web development and programming, as well as experience with frontend and backend technologies and tools. You should also have excellent problem-solving skills and the ability to work well in a team environment. If you are passionate about technology and want to make a difference in the world, we would love to hear from you!',
        'We are looking for a DevOps Engineer to join our team. You will work on various projects and tasks, such as automating infrastructure, optimizing deployment pipelines, and monitoring system performance. You should have a strong background in system administration and programming, as well as experience with DevOps tools and technologies. You should also have excellent problem-solving skills and the ability',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->exampleITJobTitles[array_rand($this->exampleITJobTitles)],
            'description' => $this->exampleITJobDescriptions[array_rand($this->exampleITJobDescriptions)],
        ];
    }
}

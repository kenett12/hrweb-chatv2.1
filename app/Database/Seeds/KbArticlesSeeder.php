<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;

class KbArticlesSeeder extends Seeder
{
    public function run()
    {
        $builder = $this->db->table('kb_articles');
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0;');
        $builder->truncate();
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1;');

        $data = [
            [
                'question' => 'Who do I contact for payroll issues?',
                'answer' => 'For salary, tax, or payslip questions, please email *hrweb@hrweb.ph**.',
                'keywords' => 'salary, pay, money, income, payslip, tax, compensation, financial',
                'is_frequent' => 1,
                'category' => 'Payroll'
            ],
            [
                'question' => 'What is the company vacation policy?',
                'answer' => 'Employees get 15 days of paid leave per year. Submit requests 2 weeks in advance.',
                'keywords' => 'vacation, leave, holiday, pto, time off, absence, break',
                'is_frequent' => 1,
                'category' => 'Leave'
            ],
            [
                'question' => 'How do I reset my work password?',
                'answer' => 'Visit the IT portal or press Ctrl+Alt+Del on your laptop to change your credentials.',
                'keywords' => 'password, login, sign in, credentials, locked, access',
                'is_frequent' => 1,
                'category' => 'IT'
            ]
        ];
        $builder->insertBatch($data);
        echo "Smart KB Seeded! 🚀\n";
    }
}
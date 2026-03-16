import os

# Quiz questions: (question, options, correct_answer)
questions = [
    {
        "question": "What is the capital of France?",
        "options": ["A. Berlin", "B. Madrid", "C. Paris", "D. Rome"],
        "answer": "C"
    },
    {
        "question": "Which planet is known as the Red Planet?",
        "options": ["A. Venus", "B. Mars", "C. Jupiter", "D. Saturn"],
        "answer": "B"
    },
    {
        "question": "What is 12 x 12?",
        "options": ["A. 132", "B. 140", "C. 144", "D. 148"],
        "answer": "C"
    },
    {
        "question": "Who wrote 'Romeo and Juliet'?",
        "options": ["A. Charles Dickens", "B. Mark Twain", "C. Homer", "D. William Shakespeare"],
        "answer": "D"
    },
    {
        "question": "What is the largest ocean on Earth?",
        "options": ["A. Atlantic Ocean", "B. Indian Ocean", "C. Pacific Ocean", "D. Arctic Ocean"],
        "answer": "C"
    },
    {
        "question": "Which gas do plants absorb from the atmosphere?",
        "options": ["A. Oxygen", "B. Nitrogen", "C. Carbon Dioxide", "D. Hydrogen"],
        "answer": "C"
    },
    {
        "question": "What is the boiling point of water in Celsius?",
        "options": ["A. 90°C", "B. 95°C", "C. 100°C", "D. 110°C"],
        "answer": "C"
    },
    {
        "question": "How many continents are there on Earth?",
        "options": ["A. 5", "B. 6", "C. 7", "D. 8"],
        "answer": "C"
    },
    {
        "question": "Which is the fastest land animal?",
        "options": ["A. Lion", "B. Horse", "C. Cheetah", "D. Leopard"],
        "answer": "C"
    },
    {
        "question": "What does CPU stand for?",
        "options": ["A. Central Process Unit", "B. Central Processing Unit", "C. Computer Personal Unit", "D. Core Processing Unit"],
        "answer": "B"
    },
]

def clear_screen():
    os.system('cls' if os.name == 'nt' else 'clear')

def display_header():
    print("=" * 50)
    print("         PYTHON QUIZ APPLICATION")
    print("=" * 50)

def run_quiz():
    clear_screen()
    display_header()
    print("\nWelcome to the Quiz.")
    print(f"Total Questions  : {len(questions)}")
    print(f"Marks per question: {100 // len(questions)}")
    print("\nRead each question carefully and choose the correct answer")
    print("by typing A, B, C, or D and pressing Enter.\n")
    input("Press Enter when you are ready to begin...")

    score = 0
    marks_per_question = 100 // len(questions)

    for i, q in enumerate(questions, 1):
        clear_screen()
        display_header()
        print(f"\nQuestion {i} of {len(questions)}")
        print("-" * 50)
        print(f"\n{q['question']}\n")
        for option in q["options"]:
            print(f"   {option}")

        print()
        while True:
            user_answer = input("Your Answer (A/B/C/D): ").strip().upper()
            if user_answer in ["A", "B", "C", "D"]:
                break
            else:
                print("Invalid input. Please enter A, B, C, or D.")

        if user_answer == q["answer"]:
            print("\nThat is correct.")
            score += 1
        else:
            print(f"\nNot quite. The correct answer was: {q['answer']}")

        input("\nPress Enter to continue...")

    # ---- Results ----
    clear_screen()
    display_header()

    total_marks = score * marks_per_question
    percentage = (score / len(questions)) * 100

    print("\n" + "=" * 50)
    print("               QUIZ RESULTS")
    print("=" * 50)
    print(f"\n  Total Questions  : {len(questions)}")
    print(f"  Correct Answers  : {score}")
    print(f"  Wrong Answers    : {len(questions) - score}")
    print(f"  Marks Obtained   : {total_marks} / 100")
    print(f"  Percentage       : {percentage:.1f}%")

    # Grade
    print("\n  Grade            : ", end="")
    if percentage >= 80:
        print("A - Excellent work.")
    elif percentage >= 60:
        print("B - Good effort.")
    elif percentage >= 40:
        print("C - A fair attempt. There is room to improve.")
    else:
        print("D - Keep at it. Every attempt is a step forward.")

    print("\n" + "=" * 50)
    print("\nThank you for taking the quiz.")

# ---- Main Entry Point ----
if __name__ == "__main__":
    while True:
        run_quiz()
        print()
        play_again = input("Would you like to try again? (yes/no): ").strip().lower()
        if play_again not in ["yes", "y"]:
            print("\nGoodbye. Keep learning and growing.")
            break
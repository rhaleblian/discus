""" twiddles on reading MIDI and writing keystrokes.
"""
import time


class Example(object):
    def __init__(self):
        import win32com.client
        self.shell = win32com.client.Dispatch('WScript.Shell')

    def foreground_key(self, key='c'):
        #shell.Run('notepad')
        #time.sleep(0.1)
        self.shell.AppActivate('notepad')
        self.shell.SendKeys(key, 0)
        #shell.SendKeys("{Enter}", 0)
        #shell.SendKeys("{F5}", 0)   # F5 prints the time/date

    @classmethod
    def background_keys(cls):
        import time
        import win32con
        import win32api
        import win32gui
        import win32process

        _, cmd = win32api.FindExecutable('notepad')

        _, _, pid, tid = win32process.CreateProcess(
            None,    # name
            cmd,     # command line
            None,    # process attributes
            None,    # thread attributes
            0,       # inheritance flag
            0,       # creation flag
            None,    # new environment
            None,    # current directory
            win32process.STARTUPINFO ())

        # wcallb is callback for EnumThreadWindows and
        # EnumChildWindows. It populates the dict 'handle' with
        # references by class name for each window and child
        # window of the given thread ID.

        def wcallb(hwnd, handle):
            handle[win32gui.GetClassName(hwnd)] = hwnd
            win32gui.EnumChildWindows(hwnd, wcallb, handle)
            return True

        handle = {}
        while not handle:   # loop until the window is loaded
            time.sleep(0.1)
            win32gui.EnumThreadWindows(tid, wcallb, handle)

        # Sending normal characters is a WM_CHAR message.
        # Function keys are sent as WM_KEYDOWN and WM_KEYUP
        # messages using the virtual keys defined in win32con,
        # such as VK_F5 for the f5 key.
        #
        # Notepad has a separate 'Edit' window to which you can
        # write, but function keys are processed by the main
        # window.

        for c in "Hello World\n":
            win32api.PostMessage(
                handle['Edit'],
                win32con.WM_CHAR,
                ord(c),
                0)

        win32api.PostMessage(
            handle['Notepad'],
            win32con.WM_KEYDOWN,
            win32con.VK_F5,
            0)
        win32api.PostMessage(
            handle['Notepad'],
            win32con.WM_KEYUP,
            win32con.VK_F5,
            0)

        # SendMessage waits for a response, but PostMessage
        # queues the message and returns immediately

    def pygame(self):
        import pygame.midi
        pygame.midi.init()
        device_id = None
        for i in range(0, pygame.midi.get_count()):
            info = pygame.midi.get_device_info(i)
            # Get the rtpMIDI input
            if info[1] == 'AVOCADO' and info[2] == 1:
                device_id = i
        print pygame.midi.get_device_info(device_id)
        inp = pygame.midi.Input(device_id)
        while True:
            #if pygame.midi.time() > 10000:
            #    # dead man
            #    break
            if inp.poll():
                event = inp.read(1)
                print event
                op, note, vel, blah = event[0][0]
                if op == 144:
                    if note == 60:  # C4 on
                        self.foreground_key('c')
                    elif note == 62:  # D4 on
                        self.foreground_key('v')
                    elif note == 12:  # C0 on
                        break
            time.sleep(0.010)  # 1 ms?
        input.close()
        pygame.midi.quit()


Example().pygame()


__author__ = 'rhaleblian'
